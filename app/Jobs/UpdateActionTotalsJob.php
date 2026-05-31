<?php

namespace App\Jobs;

use App\Models\Action;
use App\Models\ActionFundDisbursement;
use App\Models\ActionFundReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateActionTotalsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $actionUuid;

    /**
     * Create a new job instance.
     */
    public function __construct(string $actionUuid)
    {
        $this->actionUuid = $actionUuid;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $action = Action::where('uuid', $this->actionUuid)->first();
        if (!$action) {
            return;
        }

        // Total budget (somme des budgets planifiés par source de financement)
        $totalBudget = $action->fundingSources()
            ->pluck('planned_budget')
            ->filter(fn($amount) => is_numeric($amount) && $amount >= 0)
            ->sum();

        // Total encaissé
        $totalReceipt = ActionFundReceipt::where('action_uuid', $action->uuid)
            ->sum('converted_amount');

        // Total décaissé
        $totalDisbursement = ActionFundDisbursement::where('action_uuid', $action->uuid)
            ->sum('payment_amount');

        // Totals by expense type — amount split equally among types per disbursement
        // to avoid double-counting when a disbursement is tagged with multiple types.
        $expenseTypeTotals = DB::table('action_fund_disbursement_expense_types as afdet')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join(
                DB::raw('(SELECT action_fund_disbursement_uuid, COUNT(*) as type_count FROM action_fund_disbursement_expense_types GROUP BY action_fund_disbursement_uuid) as type_counts'),
                'type_counts.action_fund_disbursement_uuid', '=', 'afdet.action_fund_disbursement_uuid'
            )
            ->select('afdet.expense_type_uuid', DB::raw('SUM(afd.payment_amount / type_counts.type_count) as total'))
            ->where('afd.action_uuid', $action->uuid)
            ->groupBy('afdet.expense_type_uuid')
            ->get();

        // Update main fields
        $action->update([
            'total_budget'            => $totalBudget,
            'total_receipt_fund'      => $totalReceipt,
            'total_disbursement_fund' => $totalDisbursement,
        ]);

        // Sync expense type totals
        foreach ($expenseTypeTotals as $row) {
            $action->expenseTypes()->syncWithoutDetaching([
                $row->expense_type_uuid => ['total' => $row->total],
            ]);
        }
    }
}
