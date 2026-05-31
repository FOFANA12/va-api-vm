<?php

namespace App\Console\Commands;

use App\Models\Action;
use App\Models\ActionFundDisbursement;
use App\Models\ActionFundReceipt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncActionTotals extends Command
{
    protected $signature = 'actions:sync-totals
                            {--action= : UUID d\'une action spécifique à recalculer}
                            {--dry-run : Afficher les écarts sans appliquer les modifications}';

    protected $description = 'Réconciliation manuelle des totaux budgétaires des actions (encaissements, décaissements, budget, types de dépenses)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $targetUuid = $this->option('action');

        if ($dryRun) {
            $this->warn('Mode dry-run — aucune modification ne sera appliquée.');
        }

        $query = Action::query();

        if ($targetUuid) {
            $query->where('uuid', $targetUuid);

            if ($query->doesntExist()) {
                $this->error("Action introuvable : {$targetUuid}");
                return Command::FAILURE;
            }
        }

        $total = $query->count();
        $this->info("Actions à traiter : {$total}");

        $fixed = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(200, function ($actions) use ($dryRun, &$fixed, &$skipped, $bar) {
            foreach ($actions as $action) {
                $bar->advance();

                $computed = $this->computeTotals($action);
                $drift = $this->detectDrift($action, $computed);

                if (empty($drift)) {
                    $skipped++;
                    continue;
                }

                $this->newLine();
                $this->line("  <comment>Action #{$action->id}</comment> [{$action->uuid}] — {$action->name}");
                foreach ($drift as $field => [$stored, $expected]) {
                    $this->line("    {$field}: {$stored} → <info>{$expected}</info>");
                }

                if (!$dryRun) {
                    $this->applyTotals($action, $computed);
                }

                $fixed++;
            }
        });

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->warn("Dry-run terminé. {$fixed} action(s) avec écart détecté, {$skipped} correcte(s).");
        } else {
            $this->info("Terminé. {$fixed} action(s) corrigée(s), {$skipped} correcte(s).");
        }

        return Command::SUCCESS;
    }

    /**
     * Recalcule tous les totaux depuis les tables sources.
     */
    private function computeTotals(Action $action): array
    {
        $totalReceipt = ActionFundReceipt::where('action_uuid', $action->uuid)
            ->sum('converted_amount');

        $totalDisbursement = ActionFundDisbursement::where('action_uuid', $action->uuid)
            ->sum('payment_amount');

        $totalBudget = $action->fundingSources()
            ->pluck('planned_budget')
            ->filter(fn($v) => is_numeric($v) && $v >= 0)
            ->sum();

        // Répartition par type de dépense sans double-comptage
        $expenseTypeTotals = DB::table('action_fund_disbursement_expense_types as afdet')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join(
                DB::raw('(SELECT action_fund_disbursement_uuid, COUNT(*) as type_count
                          FROM action_fund_disbursement_expense_types
                          GROUP BY action_fund_disbursement_uuid) as type_counts'),
                'type_counts.action_fund_disbursement_uuid', '=', 'afdet.action_fund_disbursement_uuid'
            )
            ->select('afdet.expense_type_uuid', DB::raw('SUM(afd.payment_amount / type_counts.type_count) as total'))
            ->where('afd.action_uuid', $action->uuid)
            ->groupBy('afdet.expense_type_uuid')
            ->pluck('total', 'expense_type_uuid');

        return [
            'total_receipt_fund'     => round((float) $totalReceipt, 2),
            'total_disbursement_fund' => round((float) $totalDisbursement, 2),
            'total_budget'           => round((float) $totalBudget, 2),
            'expense_type_totals'    => $expenseTypeTotals,
        ];
    }

    /**
     * Compare les valeurs stockées aux valeurs calculées.
     * Retourne un tableau [champ => [stocké, attendu]] pour chaque écart.
     */
    private function detectDrift(Action $action, array $computed): array
    {
        $drift = [];

        foreach (['total_receipt_fund', 'total_disbursement_fund', 'total_budget'] as $field) {
            $stored = round((float) $action->$field, 2);
            $expected = $computed[$field];

            if (abs($stored - $expected) > 0.01) {
                $drift[$field] = [$stored, $expected];
            }
        }

        // Vérifier les totaux par type de dépense
        $storedExpenseTypes = $action->expenseTypes()->get()->pluck('pivot.total', 'uuid');
        foreach ($computed['expense_type_totals'] as $typeUuid => $expectedTotal) {
            $storedTotal = round((float) ($storedExpenseTypes[$typeUuid] ?? 0), 2);
            $expectedTotal = round((float) $expectedTotal, 2);
            if (abs($storedTotal - $expectedTotal) > 0.01) {
                $drift["expense_type[{$typeUuid}]"] = [$storedTotal, $expectedTotal];
            }
        }

        return $drift;
    }

    /**
     * Applique les totaux recalculés sur l'action.
     */
    private function applyTotals(Action $action, array $computed): void
    {
        $action->timestamps = false;
        $action->update([
            'total_receipt_fund'      => $computed['total_receipt_fund'],
            'total_disbursement_fund' => $computed['total_disbursement_fund'],
            'total_budget'            => $computed['total_budget'],
        ]);

        foreach ($computed['expense_type_totals'] as $typeUuid => $total) {
            $action->expenseTypes()->syncWithoutDetaching([
                $typeUuid => ['total' => round((float) $total, 2)],
            ]);
        }
    }
}
