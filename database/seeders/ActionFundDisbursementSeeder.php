<?php

namespace Database\Seeders;

use App\Helpers\ReferenceGenerator;
use App\Models\Action;
use App\Models\ActionFundDisbursement;
use App\Models\ActionPhase;
use App\Models\BudgetType;
use App\Models\PaymentMode;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActionFundDisbursementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $action = Action::first();
        $paymentMode = PaymentMode::first();
        $budgetType = BudgetType::first();
        $phase = ActionPhase::first();

        $supplier = Supplier::whereHas('contracts', function ($q) {
            $q->where('status', true);
        })
            ->with(['contracts' => function ($q) {
                $q->where('status', true);
            }])
            ->first();

        if (!$action || !$paymentMode || !$budgetType || !$supplier) {
            $this->command->warn('⚠️ Données manquantes : action, mode de paiement, type de budget ou fournisseur avec contrat.');
            return;
        }

        $contract = $supplier->contracts->first();

        $disbursements = [
            [
                'operation_number' => 'OP-001',
                'signature_date' => '2025-01-10',
                'execution_date' => '2025-01-20',
                'payment_date' => '2025-01-25',
                'payment_amount' => 5000.00,
                'cheque_reference' => 'CHQ-001',
                'description' => 'Premier décaissement pour formation.',
            ],
            [
                'operation_number' => 'OP-002',
                'signature_date' => '2025-02-15',
                'execution_date' => '2025-02-20',
                'payment_date' => '2025-02-28',
                'payment_amount' => 7500.00,
                'cheque_reference' => 'CHQ-002',
                'description' => 'Décaissement pour campagne de sensibilisation.',
            ],
            [
                'operation_number' => 'OP-003',
                'signature_date' => '2025-04-01',
                'execution_date' => '2025-04-10',
                'payment_date' => '2025-04-15',
                'payment_amount' => 11000.00,
                'cheque_reference' => 'CHQ-003',
                'description' => 'Décaissement pour distribution de kits.',
            ],
        ];

        foreach ($disbursements as $data) {
            $disbursement = ActionFundDisbursement::create([
                'contract_uuid' => $contract?->uuid,
                'supplier_uuid' => $supplier->uuid,
                'operation_number' => $data['operation_number'],
                'signature_date' => $data['signature_date'],
                'execution_date' => $data['execution_date'],
                'payment_date' => $data['payment_date'],
                'payment_amount' => $data['payment_amount'],
                'cheque_reference' => $data['cheque_reference'],
                'description' => $data['description'],
                'action_uuid' => $action?->uuid,
                'payment_mode_uuid' => $paymentMode?->uuid,
                'budget_type_uuid' => $budgetType?->uuid,
                'phase_uuid' => $phase?->uuid,
            ]);

            $action = $disbursement->action;
            $disbursement->update([
                'reference' => ReferenceGenerator::generateActionFundDisbursementReference($disbursement, $action),
            ]);
        }
    }
}
