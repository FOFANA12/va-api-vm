<?php

namespace App\Repositories\Report;

use App\Models\Action;
use App\Models\Structure;
use Carbon\Carbon;

class StructurePerformanceReportRepository
{
    /**
     * Get full reporting (budget + performance + delays) for a specific structure.
     */
    public function getReport(Structure $structure): array
    {

        $structures[] = $structure->uuid;
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);
        $structureUuids = collect($structures)->pluck('uuid')->toArray();

        $actions = Action::whereIn('structure_uuid', $structureUuids)
            ->with(['currency', 'metric'])
            ->get();

        $plannedBudget = 0;
        $acquiredBudget = 0;
        $spentBudget = 0;
        $currency = null;

        $rates = [];
        $indexes = [];
        $durationVariances = [];
        $durationOverrunRates = [];
        $delayVariances = [];
        $delayOverrunRates = [];

        foreach ($actions as $action) {
            // --- Budgets ---
            $plannedBudget += $action->total_budget ?? 0;
            $acquiredBudget += $action->total_receipt_fund ?? 0;
            $spentBudget += $action->total_disbursement_fund ?? 0;

            $currency = $currency ?? optional($action->currency)->code;

            // --- Metrics ---
            if ($action->metric) {
                $rates[] = $action->metric->realization_rate ?? 0;
                $indexes[] = $action->metric->realization_index ?? 0;
            }

            // --- Dates ---
            $startPlanned = $action->start_date ? Carbon::parse($action->start_date) : null;
            $endPlanned = $action->end_date ? Carbon::parse($action->end_date) : null;
            $actualStart = $action->actual_start_date ? Carbon::parse($action->actual_start_date) : null;
            $actualEnd = $action->actual_end_date ? Carbon::parse($action->actual_end_date) : null;

            // Planned duration
            $plannedDuration = ($startPlanned && $endPlanned)
                ? $startPlanned->diffInDays($endPlanned)
                : null;

            // Actual duration
            $actualDuration = ($actualStart && $actualEnd)
                ? $actualStart->diffInDays($actualEnd)
                : null;

            // Duration variance (days) + overrun rate
            if ($plannedDuration !== null && $actualDuration !== null) {
                $durationVariances[] = $actualDuration - $plannedDuration;

                $rate = $plannedDuration > 0
                    ? (($actualDuration - $plannedDuration) / $plannedDuration) * 100
                    : 0;
                $durationOverrunRates[] = $rate;
            }

            // Delay variance (days) + overrun rate
            if ($endPlanned && $actualEnd) {
                $delayVariances[] = $endPlanned->diffInDays($actualEnd, false);

                $plannedEndDays = $startPlanned ? $startPlanned->diffInDays($endPlanned) : null;
                if ($plannedEndDays && $plannedEndDays > 0) {
                    $rate = (($actualEnd->diffInDays($endPlanned, false)) / $plannedEndDays) * 100;
                    $delayOverrunRates[] = $rate;
                }
            }
        }

        // --- Budget calculations ---
        $budgetToMobilize = max($plannedBudget - $acquiredBudget, 0);
        $availableBudget  = max($acquiredBudget - $spentBudget, 0);
        $disbursementRate = $acquiredBudget > 0
            ? round(($spentBudget / $acquiredBudget) * 100, 2)
            : 0;

        // --- Performance calculations ---
        $realizationRate = !empty($rates) ? round(array_sum($rates) / count($rates), 2) : 0;
        $realizationIndex = !empty($indexes) ? round(array_sum($indexes) / count($indexes), 2) : 0;

        // --- Delay & duration calculations ---
        $durationVarianceDays = !empty($durationVariances)
            ? round(array_sum($durationVariances) / count($durationVariances), 2)
            : 0;

        $durationOverrunRate = !empty($durationOverrunRates)
            ? round(array_sum($durationOverrunRates) / count($durationOverrunRates), 2)
            : 0;

        $delayVarianceDays = !empty($delayVariances)
            ? round(array_sum($delayVariances) / count($delayVariances), 2)
            : 0;

        $delayOverrunRate = !empty($delayOverrunRates)
            ? round(array_sum($delayOverrunRates) / count($delayOverrunRates), 2)
            : 0;

        return [
            'planned_budget' => $plannedBudget,
            'acquired_budget' => $acquiredBudget,
            'spent_budget' => $spentBudget,
            'budget_to_mobilize' => $budgetToMobilize,
            'available_budget' => $availableBudget,
            'disbursement_rate' => $disbursementRate,
            'realization_rate' => $realizationRate,
            'realization_index' => $realizationIndex,
            'currency' => $currency,
            'duration_variance_days' => $durationVarianceDays,
            'duration_overrun_rate' => $durationOverrunRate,
            'delay_variance_days' => $delayVarianceDays,
            'delay_overrun_rate' => $delayOverrunRate,
        ];
    }
}
