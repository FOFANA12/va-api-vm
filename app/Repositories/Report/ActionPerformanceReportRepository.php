<?php

namespace App\Repositories\Report;

use App\Helpers\DateTimeFormatter;
use App\Models\Action;
use App\Support\ChartType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActionPerformanceReportRepository
{
    /**
     * Get budget reporting for a specific action
     */
    public function getBudgetReport(Action $action): array
    {
        // Values already stored on the action
        $plannedBudget = $action->total_budget ?? 0;
        $acquiredBudget = $action->total_receipt_fund ?? 0;
        $spentBudget = $action->total_disbursement_fund ?? 0;

        // Budget to be mobilized
        $budgetToMobilize = max($plannedBudget - $acquiredBudget, 0);

        // Available budget
        $availableBudget = max($acquiredBudget - $spentBudget, 0);

        // Disbursement rate
        $disbursementRate = $acquiredBudget > 0
            ? round(($spentBudget / $acquiredBudget) * 100, 2)
            : 0;

        // Cost variance
        $costVariance = $spentBudget - $plannedBudget;

        // Overrun rate
        $overrunRate = $plannedBudget > 0
            ? round((($spentBudget - $plannedBudget) / $plannedBudget) * 100, 2)
            : -100;

        // Breakdown of disbursements by expense type.
        // Each disbursement's payment_amount is divided equally among its expense types
        // to avoid double-counting when a single disbursement is tagged with multiple types.
        $disbursementByTypes = DB::table('action_fund_disbursement_expense_types as afdet')
            ->join('action_fund_disbursements as afd', 'afd.uuid', '=', 'afdet.action_fund_disbursement_uuid')
            ->join('expense_types as et', 'afdet.expense_type_uuid', '=', 'et.uuid')
            ->join(
                DB::raw('(SELECT action_fund_disbursement_uuid, COUNT(*) as type_count FROM action_fund_disbursement_expense_types GROUP BY action_fund_disbursement_uuid) as type_counts'),
                'type_counts.action_fund_disbursement_uuid', '=', 'afdet.action_fund_disbursement_uuid'
            )
            ->select('et.name as type', DB::raw('SUM(afd.payment_amount / type_counts.type_count) as total'))
            ->where('afd.action_uuid', $action->uuid)
            ->groupBy('et.uuid', 'et.name')
            ->get()->map(function ($row) use ($spentBudget) {
                return [
                    'type' => $row->type,
                    'total' => round((float) $row->total, 2),
                    'percent' => $spentBudget > 0
                        ? round(($row->total / $spentBudget) * 100, 2)
                        : 0,
                ];
            });

        return [
            'planned_budget' => $plannedBudget,
            'acquired_budget' => $acquiredBudget,
            'spent_budget' => $spentBudget,
            'budget_to_mobilize' => $budgetToMobilize,
            'available_budget' => $availableBudget,
            'disbursement_rate' => $disbursementRate,
            'cost_variance' => $costVariance,
            'overrun_rate' => $overrunRate,
            'disbursement_types' => $disbursementByTypes,
            'currency' => $action->currency
        ];
    }

    /**
     * Get progress reporting (planned vs actual) for a specific action
     */
    public function getProgressReport(Action $action): array
    {
        $rows = [];
        $overallProgress = 0;

        foreach ($action->periods as $period) {
            $planned = (float) $period->progress_percent; // Planned (P)

            $hasControl = $period->controls !== null;

            $realized = null;
            $variance = null;
            $performanceIndex = null;

            if ($hasControl) {
                $realized = (float) $period->actual_progress_percent;

                // Tr - P
                $variance = round($realized - $planned, 2);

                // Tr / P
                $performanceIndex = $planned > 0
                    ? round($realized / $planned, 2)
                    : 0;

                $overallProgress = max($overallProgress, $realized);
            }


            $rows[] = [
                'period' => DateTimeFormatter::formatDate($period->start_date) . ' - ' . DateTimeFormatter::formatDate($period->end_date),
                'planned_percent' => $planned,
                'realized_percent' => $realized,
                'variance' => $variance,
                'performance_index' => $performanceIndex,
            ];

            $overallProgress = max($overallProgress, $realized);
        }

        return [
            'rows' => $rows,
            'overall_progress' => $overallProgress,
            'actual_progress_percent' => (float) $action->actual_progress_percent,
            'chart_type' => ChartType::get($action->chart_type, app()->getLocale()),
            'unit' => '%',
        ];
    }

    /**
     * Get delay reporting for a specific action
     */
    public function getDelayReport(Action $action): array
    {
        $today = Carbon::today();
        $startPlanned = $action->start_date ? Carbon::parse($action->start_date) : null;
        $endPlanned = $action->end_date ? Carbon::parse($action->end_date) : null;
        $actualStart = $action->actual_start_date ? Carbon::parse($action->actual_start_date) : null;
        $actualEnd = $action->actual_end_date ? Carbon::parse($action->actual_end_date) : null;

        // Planned duration
        $plannedDuration = ($startPlanned && $endPlanned)
            ? $startPlanned->diffInDays($endPlanned)
            : null;

        // Days to start
        $daysToStart = $startPlanned
            ? $today->diffInDays($startPlanned, false)
            : null;

        // In-progress remaining days
        $remainingDays = ($endPlanned && !$actualEnd)
            ? $today->diffInDays($endPlanned, false)
            : null;

        // Start delay
        $startDelay = ($startPlanned && $actualStart)
            ? $startPlanned->diffInDays($actualStart, false)
            : null;

        // Completed duration + variance
        $actualDuration = ($actualStart && $actualEnd)
            ? $actualStart->diffInDays($actualEnd)
            : null;

        return [
            'planned_start' => $startPlanned ? DateTimeFormatter::formatDate($startPlanned) : null,
            'planned_end' => $endPlanned ? DateTimeFormatter::formatDate($endPlanned) : null,
            'planned_duration_days' => $plannedDuration,
            'days_to_start' => $daysToStart,
            'actual_start' => $actualStart ? DateTimeFormatter::formatDate($actualStart) : null,
            'actual_end' => $actualEnd ? DateTimeFormatter::formatDate($actualEnd) : null,
            'actual_duration_days' => $actualDuration,
            'remaining_days' => $remainingDays,
            'start_delay_days' => $startDelay,
        ];
    }
}
