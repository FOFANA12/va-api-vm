<?php

namespace App\Repositories\Report;

use App\Helpers\DateTimeFormatter;
use App\Models\Indicator;
use App\Support\ChartType;
use Carbon\Carbon;

class IndicatorPerformanceReportRepository
{
    /**
     * Get progress reporting (target vs achieved) for a specific indicator
     */
    public function getProgressReport(Indicator $indicator): array
    {
        $rows = [];
        $overallProgress = 0;

        foreach ($indicator->periods as $period) {
            $target = (float) $period->target_value;

            $hasControl = $period->controls && $period->controls->count() > 0;

            $achieved = null;
            $variance = null;
            $performanceIndex = null;

            if ($hasControl) {
                $achieved = (float) $period->achieved_value;

                // VA - VC
                $variance = round($achieved - $target, 2);

                // VA / VC
                $performanceIndex = $target > 0
                    ? round($achieved / $target, 2)
                    : 0;

                $overallProgress = max($overallProgress, $achieved);
            }

            $rows[] = [
                'period' => DateTimeFormatter::formatDate($period->start_date) . ' - ' . DateTimeFormatter::formatDate($period->end_date),
                'target_value' => $target,
                'achieved_value' => $achieved,
                'variance' => $variance,
                'performance_index' => $performanceIndex,
            ];

            $overallProgress = max($overallProgress, $achieved);
        }

        return [
            'rows' => $rows,
            'overall_progress' => $overallProgress,
            'final_target_value' => (float) $indicator->final_target_value,
            'chart_type' => ChartType::get($indicator->chart_type, app()->getLocale()),
            'unit' => $indicator->unit,
        ];
    }

    /**
     * Get delay reporting for a specific indicator
     */
    public function getDelayReport(Indicator $indicator): array
    {
        $today = Carbon::today();
        $startPlanned = $indicator->strategicObjective?->start_date
            ?  Carbon::parse($indicator->strategicObjective->start_date)
            : null;

        $endPlanned = $indicator->strategicObjective?->end_date
            ? Carbon::parse($indicator->strategicObjective->end_date)
            : null;

        $actualStart = $indicator->actual_start_date
            ? Carbon::parse($indicator->actual_start_date)
            : null;

        $actualEnd = $indicator->actual_end_date
            ? Carbon::parse($indicator->actual_end_date)
            : null;

        $plannedDuration = ($startPlanned && $endPlanned)
            ? $startPlanned->diffInDays($endPlanned)
            : null;

        $daysToStart = $startPlanned
            ? $today->diffInDays($startPlanned, false)
            : null;

        $remainingDays = ($endPlanned && !$actualEnd)
            ? $today->diffInDays($endPlanned, false)
            : null;

        $startDelay = ($startPlanned && $actualStart)
            ? $startPlanned->diffInDays($actualStart, false)
            : null;

        $actualDuration = ($actualStart && $actualEnd)
            ? $actualStart->diffInDays($actualEnd)
            : null;

        $durationVariance = ($endPlanned && $actualEnd)
            ? $endPlanned->diffInDays($actualEnd, false)
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
            'duration_variance_days' => $durationVariance,
        ];
    }
}
