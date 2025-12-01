<?php

namespace Database\Seeders;

use App\Helpers\ReferenceGenerator;
use App\Models\ActionFundReceipt;
use App\Models\Action;
use App\Models\FundingSource;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ActionFundReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ActionFundReceipt::whereNotNull('id')->delete();

        $action = Action::first();
        $fundingSource = FundingSource::first();
        $currency = Currency::first();

        if ($action && $fundingSource && $currency) {
            $receipts = [
                [
                    'reference' => 'AFR-2025-001',
                    'action_uuid' => $action->uuid,
                    'funding_source_uuid' => $fundingSource->uuid,
                    'currency_uuid' => $currency->uuid,
                    'exchange_rate' => 1.0,
                    'amount_original' => 1500,
                    'converted_amount' => 1500,
                    'receipt_date' => '2025-01-10',
                    'validity_date' => '2025-12-31',
                    'created_by' => null,
                    'updated_by' => null,
                ],
                [
                    'reference' => 'AFR-2025-002',
                    'action_uuid' => $action->uuid,
                    'funding_source_uuid' => $fundingSource->uuid,
                    'currency_uuid' => $currency->uuid,
                    'exchange_rate' => 1,
                    'amount_original' => 1200,
                    'converted_amount' => 1200,
                    'receipt_date' => '2025-02-15',
                    'validity_date' => '2025-11-30',
                    'created_by' => null,
                    'updated_by' => null,
                ],
            ];

            foreach ($receipts as $receipt) {
                $receipt = ActionFundReceipt::create($receipt);

                $action = $receipt->action;
                $receipt->update([
                    'reference' => ReferenceGenerator::generateFundReceiptReference($receipt->id, $action),
                ]);
            }
        }
    }
}
