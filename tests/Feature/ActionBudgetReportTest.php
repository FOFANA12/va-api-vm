<?php

namespace Tests\Feature;

use App\Exceptions\DomainException;
use App\Models\Action;
use App\Models\ActionFundDisbursement;
use App\Models\ActionFundReceipt;
use App\Models\ActionPlan;
use App\Models\ActionPhase;
use App\Models\BudgetType;
use App\Models\Contract;
use App\Models\Currency;
use App\Models\DelegatedProjectOwner;
use App\Models\ExpenseType;
use App\Models\FundingSource;
use App\Models\PaymentMode;
use App\Models\ProjectOwner;
use App\Models\Structure;
use App\Models\Supplier;
use App\Models\User;
use App\Jobs\UpdateActionTotalsJob;
use App\Repositories\ActionFundDisbursementRepository;
use App\Repositories\ActionFundReceiptRepository;
use App\Repositories\Report\ActionPerformanceReportRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActionBudgetReportTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Cas de base : les totaux du rapport reflètent les encaissements/décaissements
    // -------------------------------------------------------------------------

    public function test_budget_report_totals_match_receipts_and_disbursements(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 100_000);

        $this->addReceipt($action, $deps, 30_000);
        $this->addReceipt($action, $deps, 20_000);
        $this->addDisbursement($action, $deps, 15_000, [$deps['expA']->uuid]);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $this->assertEquals(100_000, $report['planned_budget']);
        $this->assertEquals(50_000, $report['acquired_budget']);
        $this->assertEquals(15_000, $report['spent_budget']);
        $this->assertEquals(50_000, $report['budget_to_mobilize']);  // 100k - 50k
        $this->assertEquals(35_000, $report['available_budget']);    // 50k - 15k
        $this->assertEquals(30.0, $report['disbursement_rate']);     // 15k/50k * 100
        $this->assertEquals(-85_000, $report['cost_variance']);      // 15k - 100k
        $this->assertEquals(-85.0, $report['overrun_rate']);         // (15k-100k)/100k * 100
    }

    // -------------------------------------------------------------------------
    // Bug #1 : deux décaissements du même montant ne doivent PAS être dédupliqués
    // SUM(DISTINCT payment_amount) était la cause — doit maintenant être 4000 et non 2000
    // -------------------------------------------------------------------------

    public function test_budget_report_does_not_deduplicate_same_amount_disbursements(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $this->addReceipt($action, $deps, 50_000);

        // Deux décaissements identiques (2000 chacun) → doit totaliser 4000 pour Depense A
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $typeA = collect($report['disbursement_types'])->firstWhere('type', 'Depense A');

        $this->assertEquals(4_000, $report['spent_budget'], 'spent_budget doit être la somme réelle');
        $this->assertNotNull($typeA, 'Le type Depense A doit apparaître dans le rapport');
        $this->assertEquals(4_000, $typeA['total'], 'Depense A doit totaliser 4000 (2 x 2000), pas 2000');
    }

    // -------------------------------------------------------------------------
    // Bug #2 : un décaissement avec 2 types ne doit pas être double-compté.
    // La somme des totaux par type doit être égale à spent_budget.
    // -------------------------------------------------------------------------

    public function test_budget_report_types_sum_equals_spent_budget_with_multi_type_disbursement(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $this->addReceipt($action, $deps, 50_000);

        // Décaissement A (2000, type A) + Décaissement B (2000, type A) + Décaissement C (3000, types B+C)
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);
        $this->addDisbursement($action, $deps, 3_000, [$deps['expB']->uuid, $deps['expC']->uuid]);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $sumByTypes = collect($report['disbursement_types'])->sum('total');

        // Attendu : Depense A = 4000, Depense B = 1500, Depense C = 1500 → total = 7000
        $this->assertEquals(7_000, $report['spent_budget']);
        $this->assertEqualsWithDelta($report['spent_budget'], $sumByTypes, 0.01,
            'La somme des types doit être égale à spent_budget (pas de double-comptage)');
    }

    // -------------------------------------------------------------------------
    // La répartition correcte quand un décaissement est lié à plusieurs types
    // -------------------------------------------------------------------------

    public function test_budget_report_splits_amount_equally_across_expense_types(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 10_000);
        $this->addReceipt($action, $deps, 10_000);

        // 3000 MRU répartis sur 2 types → 1500 chacun
        $this->addDisbursement($action, $deps, 3_000, [$deps['expB']->uuid, $deps['expC']->uuid]);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());
        $types = collect($report['disbursement_types'])->keyBy('type');

        $this->assertEqualsWithDelta(1_500, $types['Depense B']['total'], 0.01);
        $this->assertEqualsWithDelta(1_500, $types['Depense C']['total'], 0.01);
    }

    // -------------------------------------------------------------------------
    // Cas budget_to_mobilize et available_budget ne passent jamais en négatif
    // -------------------------------------------------------------------------

    public function test_budget_to_mobilize_and_available_budget_are_never_negative(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Cas extrême : plus dépensé qu'encaissé, et plus encaissé que budgété
        [$action, $deps] = $this->createAction($user, totalBudget: 1_000);
        $this->addReceipt($action, $deps, 5_000);
        $this->addDisbursement($action, $deps, 8_000, [$deps['expA']->uuid]);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $this->assertGreaterThanOrEqual(0, $report['budget_to_mobilize'], 'budget_to_mobilize ne peut pas être négatif');
        $this->assertGreaterThanOrEqual(0, $report['available_budget'], 'available_budget ne peut pas être négatif');
    }

    // -------------------------------------------------------------------------
    // Cas sans décaissements : disbursement_rate = 0, types = []
    // -------------------------------------------------------------------------

    public function test_budget_report_with_no_disbursements(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $this->addReceipt($action, $deps, 20_000);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $this->assertEquals(0, $report['spent_budget']);
        $this->assertEquals(0, $report['disbursement_rate']);
        $this->assertEmpty($report['disbursement_types']);
        $this->assertEquals(30_000, $report['budget_to_mobilize']);
        $this->assertEquals(20_000, $report['available_budget']);
    }

    // -------------------------------------------------------------------------
    // Cas sans encaissements ni décaissements : tout à zéro
    // -------------------------------------------------------------------------

    public function test_budget_report_with_no_funds_at_all(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action] = $this->createAction($user, totalBudget: 100_000);

        $report = app(ActionPerformanceReportRepository::class)->getBudgetReport($action->fresh());

        $this->assertEquals(100_000, $report['planned_budget']);
        $this->assertEquals(0, $report['acquired_budget']);
        $this->assertEquals(0, $report['spent_budget']);
        $this->assertEquals(100_000, $report['budget_to_mobilize']);
        $this->assertEquals(0, $report['available_budget']);
        $this->assertEquals(0, $report['disbursement_rate']);
        $this->assertEmpty($report['disbursement_types']);
    }

    // =========================================================================
    // Tests sur updateActionTotalDisbursementFund / updateActionTotalReceiptFund
    // =========================================================================

    // Bug #1 : destroy() des décaissements ne recalculait pas total_disbursement_fund
    public function test_destroy_disbursement_recalculates_total_disbursement_fund(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $d1 = $this->addDisbursement($action, $deps, 5_000, [$deps['expA']->uuid]);
        $d2 = $this->addDisbursement($action, $deps, 3_000, [$deps['expA']->uuid]);

        $this->assertEquals(8_000, $action->fresh()->total_disbursement_fund);

        // Suppression via le repository (chemin réel)
        $request = Request::create('/api/action-fund-disbursements', 'POST', [
            'ids' => [$d1->id],
        ]);
        app(ActionFundDisbursementRepository::class)->destroy($request);

        $this->assertEquals(3_000, $action->fresh()->total_disbursement_fund,
            'total_disbursement_fund doit être recalculé après destroy()');
    }

    // Bug #1 symétrique : destroy() des encaissements recalcule bien (vérification de non-régression)
    public function test_destroy_receipt_recalculates_total_receipt_fund(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $r1 = $this->addReceipt($action, $deps, 20_000);
        $r2 = $this->addReceipt($action, $deps, 10_000);

        $this->assertEquals(30_000, $action->fresh()->total_receipt_fund);

        $request = Request::create('/api/action-fund-receipts', 'POST', [
            'ids' => [$r1->id],
        ]);
        app(ActionFundReceiptRepository::class)->destroy($request);

        $this->assertEquals(10_000, $action->fresh()->total_receipt_fund,
            'total_receipt_fund doit être recalculé après destroy()');
    }

    public function test_destroy_receipt_exposes_business_error_when_action_is_closed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $receipt = $this->addReceipt($action, $deps, 20_000);
        $action->update(['status' => 'closed']);

        $request = Request::create('/api/action-fund-receipts', 'POST', [
            'ids' => [$receipt->id],
        ]);

        try {
            app(ActionFundReceiptRepository::class)->destroy($request);
            $this->fail('La suppression devait etre refusee pour une action cloturee.');
        } catch (DomainException $e) {
            $this->assertSame(
                __('app/action_fund_receipt.errors.action_locked'),
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('action_fund_receipts', ['id' => $receipt->id]);
    }

    public function test_destroy_receipt_exposes_business_error_when_action_is_planned(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $receipt = $this->addReceipt($action, $deps, 20_000);
        $action->update(['status' => 'planned']);

        $request = Request::create('/api/action-fund-receipts', 'POST', [
            'ids' => [$receipt->id],
        ]);

        try {
            app(ActionFundReceiptRepository::class)->destroy($request);
            $this->fail('La suppression devait etre refusee pour une action planifiee.');
        } catch (DomainException $e) {
            $this->assertSame(
                __('app/action_fund_receipt.errors.action_locked'),
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('action_fund_receipts', ['id' => $receipt->id]);
    }

    public function test_financial_requirements_only_offer_in_progress_actions_for_creation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action] = $this->createAction($user, totalBudget: 50_000);
        $action->update(['reference' => 'ACT-TEST-020']);

        $receiptRequest = Request::create('/api/action-fund-receipts/requirements', 'GET', [
            'mode' => 'create',
        ]);
        $disbursementRequest = Request::create('/api/action-fund-disbursements/requirements', 'GET', [
            'mode' => 'create',
        ]);

        $this->assertSame(
            [$action->uuid],
            app(ActionFundReceiptRepository::class)->requirements($receiptRequest)['actions']->pluck('uuid')->all()
        );
        $this->assertSame(
            [$action->uuid],
            app(ActionFundDisbursementRepository::class)->requirements($disbursementRequest)['actions']->pluck('uuid')->all()
        );
        $this->assertSame(
            'ACT-TEST-020 - ' . $action->name,
            app(ActionFundReceiptRepository::class)->requirements($receiptRequest)['actions']->first()->label
        );
        $this->assertSame(
            'ACT-TEST-020 - ' . $action->name,
            app(ActionFundDisbursementRepository::class)->requirements($disbursementRequest)['actions']->first()->label
        );

        $action->update(['status' => 'planned']);

        $this->assertEmpty(app(ActionFundReceiptRepository::class)->requirements($receiptRequest)['actions']);
        $this->assertEmpty(app(ActionFundDisbursementRepository::class)->requirements($disbursementRequest)['actions']);
    }

    // Bug #3 : UpdateActionTotalsJob doit être dispatché après destroy() d'un décaissement
    public function test_destroy_disbursement_dispatches_update_totals_job(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 10_000);
        $d = $this->addDisbursement($action, $deps, 1_000, [$deps['expA']->uuid]);

        Bus::fake(); // reset pour ne capturer que le destroy
        $request = Request::create('/api/action-fund-disbursements', 'POST', [
            'ids' => [$d->id],
        ]);
        app(ActionFundDisbursementRepository::class)->destroy($request);

        Bus::assertDispatched(UpdateActionTotalsJob::class, function ($job) use ($action) {
            return $job->actionUuid === $action->uuid;
        });
    }

    public function test_destroy_disbursement_exposes_business_error_when_action_is_stopped(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 10_000);
        $disbursement = $this->addDisbursement($action, $deps, 1_000, [$deps['expA']->uuid]);
        $action->update(['status' => 'stopped']);

        $request = Request::create('/api/action-fund-disbursements', 'POST', [
            'ids' => [$disbursement->id],
        ]);

        try {
            app(ActionFundDisbursementRepository::class)->destroy($request);
            $this->fail('La suppression devait etre refusee pour une action en arret.');
        } catch (DomainException $e) {
            $this->assertSame(
                __('app/action_fund_disbursement.errors.action_locked'),
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('action_fund_disbursements', ['id' => $disbursement->id]);
    }

    // Bug #2 : UpdateActionTotalsJob ne doit pas double-compter les expense types
    public function test_update_totals_job_does_not_double_count_expense_types(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 10_000);

        // 4000 MRU sur Depense A (2 x 2000)
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);
        $this->addDisbursement($action, $deps, 2_000, [$deps['expA']->uuid]);
        // 3000 MRU sur Depense B + Depense C
        $this->addDisbursement($action, $deps, 3_000, [$deps['expB']->uuid, $deps['expC']->uuid]);

        // Exécuter le job directement (synchrone)
        (new UpdateActionTotalsJob($action->uuid))->handle();

        $types = $action->expenseTypes()->get()->keyBy('uuid');

        $totalA = $types[$deps['expA']->uuid]->pivot->total ?? null;
        $totalB = $types[$deps['expB']->uuid]->pivot->total ?? null;
        $totalC = $types[$deps['expC']->uuid]->pivot->total ?? null;

        $this->assertEqualsWithDelta(4_000, $totalA, 0.01, 'Depense A doit être 4000 (2x2000)');
        $this->assertEqualsWithDelta(1_500, $totalB, 0.01, 'Depense B doit être 1500 (3000/2)');
        $this->assertEqualsWithDelta(1_500, $totalC, 0.01, 'Depense C doit être 1500 (3000/2)');
        $this->assertEqualsWithDelta(7_000, $totalA + $totalB + $totalC, 0.01,
            'La somme des expense types doit égaler total_disbursement_fund');
    }

    public function test_maintenance_calculations_refresh_totals_without_processing_a_queue(): void
    {
        config(['queue.default' => 'database']);

        $user = User::factory()->create();
        $this->actingAs($user);

        [$action, $deps] = $this->createAction($user, totalBudget: 50_000);
        $this->addReceipt($action, $deps, 20_000);
        $this->addDisbursement($action, $deps, 4_000, [$deps['expA']->uuid]);

        $action->update([
            'total_receipt_fund' => 0,
            'total_disbursement_fund' => 0,
        ]);

        $this->assertSame(0, DB::table('jobs')->count());

        $this->artisan('maintenance:run-background-calculations')->assertSuccessful();

        $action->refresh();
        $this->assertEquals(20_000, $action->total_receipt_fund);
        $this->assertEquals(4_000, $action->total_disbursement_fund);
        $this->assertSame(0, DB::table('jobs')->count());
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Crée une action avec toutes ses dépendances.
     * Retourne [$action, $deps] où $deps contient les objets réutilisables.
     */
    private function createAction(User $user, float $totalBudget = 0): array
    {
        $structure = Structure::create([
            'uuid'         => (string) Str::uuid(),
            'abbreviation' => 'TST',
            'name'         => 'Structure Test ' . Str::random(4),
            'type'         => 'STATE',
            'status'       => true,
            'created_by'   => $user->uuid,
            'updated_by'   => $user->uuid,
        ]);

        $actionPlan = ActionPlan::create([
            'uuid'           => (string) Str::uuid(),
            'structure_uuid' => $structure->uuid,
            'name'           => 'Plan Test ' . Str::random(4),
            'start_date'     => '2026-01-01',
            'end_date'       => '2027-12-31',
            'created_by'     => $user->uuid,
            'updated_by'     => $user->uuid,
        ]);

        $projectOwner = ProjectOwner::create([
            'uuid'           => (string) Str::uuid(),
            'structure_uuid' => $structure->uuid,
            'name'           => 'MO Test ' . Str::random(4),
            'status'         => true,
            'created_by'     => $user->uuid,
            'updated_by'     => $user->uuid,
        ]);

        $delegated = DelegatedProjectOwner::create([
            'uuid'                => (string) Str::uuid(),
            'project_owner_uuid'  => $projectOwner->uuid,
            'name'                => 'MOD Test ' . Str::random(4),
            'status'              => true,
            'created_by'          => $user->uuid,
            'updated_by'          => $user->uuid,
        ]);

        $action = Action::create([
            'uuid'                            => (string) Str::uuid(),
            'structure_uuid'                  => $structure->uuid,
            'action_plan_uuid'                => $actionPlan->uuid,
            'project_owner_uuid'              => $projectOwner->uuid,
            'delegated_project_owner_uuid'    => $delegated->uuid,
            'name'                            => 'Action Test ' . Str::random(4),
            'status'                          => 'in_progress',
            'currency'                        => 'MRU',
            'total_budget'                    => $totalBudget,
            'total_receipt_fund'              => 0,
            'total_disbursement_fund'         => 0,
            'created_by'                      => $user->uuid,
            'updated_by'                      => $user->uuid,
        ]);

        $phase = ActionPhase::create([
            'uuid'        => (string) Str::uuid(),
            'action_uuid' => $action->uuid,
            'name'        => 'Phase Test',
            'number'      => 1,
            'start_date'  => '2026-01-01',
            'end_date'    => '2027-12-31',
            'weight'      => 100,
            'created_by'  => $user->uuid,
            'updated_by'  => $user->uuid,
        ]);

        $currency = Currency::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'MRU Test',
            'code'       => 'MRU',
            'is_default' => true,
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $fundingSource = FundingSource::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Source Test ' . Str::random(4),
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $paymentMode = PaymentMode::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Virement Test ' . Str::random(4),
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $budgetType = BudgetType::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Budget Test ' . Str::random(4),
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $supplier = Supplier::factory()->create([
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $contract = Contract::factory()->create([
            'supplier_uuid' => $supplier->uuid,
            'created_by'    => $user->uuid,
            'updated_by'    => $user->uuid,
        ]);

        $expA = ExpenseType::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Depense A',
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $expB = ExpenseType::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Depense B',
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $expC = ExpenseType::create([
            'uuid'       => (string) Str::uuid(),
            'name'       => 'Depense C',
            'status'     => true,
            'created_by' => $user->uuid,
            'updated_by' => $user->uuid,
        ]);

        $deps = compact('structure', 'actionPlan', 'projectOwner', 'delegated',
            'phase', 'currency', 'fundingSource', 'paymentMode', 'budgetType',
            'supplier', 'contract', 'expA', 'expB', 'expC');

        return [$action, $deps];
    }

    /**
     * Crée un encaissement et met à jour le total sur l'action.
     */
    private function addReceipt(Action $action, array $deps, float $amount): ActionFundReceipt
    {
        $receipt = ActionFundReceipt::create([
            'uuid'                => (string) Str::uuid(),
            'action_uuid'         => $action->uuid,
            'funding_source_uuid' => $deps['fundingSource']->uuid,
            'currency_uuid'       => $deps['currency']->uuid,
            'exchange_rate'       => 1,
            'amount_original'     => $amount,
            'converted_amount'    => $amount,
            'receipt_date'        => '2026-05-01',
            'validity_date'       => '2027-05-01',
            'created_by'          => $action->created_by,
            'updated_by'          => $action->created_by,
        ]);

        $total = ActionFundReceipt::where('action_uuid', $action->uuid)->sum('converted_amount');
        Action::where('uuid', $action->uuid)->update(['total_receipt_fund' => $total]);

        return $receipt;
    }

    /**
     * Crée un décaissement avec ses types de dépenses et met à jour le total sur l'action.
     *
     * @param string[] $expenseTypeUuids
     */
    private function addDisbursement(Action $action, array $deps, float $amount, array $expenseTypeUuids): ActionFundDisbursement
    {
        static $opCounter = 0;
        $opCounter++;

        $disbursement = ActionFundDisbursement::create([
            'uuid'              => (string) Str::uuid(),
            'action_uuid'       => $action->uuid,
            'operation_number'  => 'OP-TEST-' . str_pad($opCounter, 3, '0', STR_PAD_LEFT),
            'signature_date'    => '2026-05-01',
            'execution_date'    => '2026-05-05',
            'payment_date'      => '2026-05-10',
            'payment_amount'    => $amount,
            'payment_mode_uuid' => $deps['paymentMode']->uuid,
            'cheque_reference'  => 'CHQ-' . $opCounter,
            'budget_type_uuid'  => $deps['budgetType']->uuid,
            'phase_uuid'        => $deps['phase']->uuid,
            'supplier_uuid'     => $deps['supplier']->uuid,
            'contract_uuid'     => $deps['contract']->uuid,
            'created_by'        => $action->created_by,
            'updated_by'        => $action->created_by,
        ]);

        $disbursement->expenseTypes()->sync($expenseTypeUuids);

        $total = ActionFundDisbursement::where('action_uuid', $action->uuid)->sum('payment_amount');
        Action::where('uuid', $action->uuid)->update(['total_disbursement_fund' => $total]);

        return $disbursement;
    }
}
