<?php

namespace App\Jobs;

use App\Models\ActionObjectiveAlignment;
use App\Models\IndicatorPeriod;
use App\Models\StrategicObjective;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EvaluateStrategicObjectiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $objectiveUuid;

    public function __construct(string $objectiveUuid)
    {
        $this->objectiveUuid = $objectiveUuid;
    }

    public function handle(): void
    {
        $today = Carbon::today();
        $next7Days = $today->copy()->addDays(7);

        $objective = StrategicObjective::where('uuid', $this->objectiveUuid)
            ->with('indicators.periods.controls')
            ->first();

        if (!$objective) {
            return;
        }

        $indicatorUuids = $objective->indicators->pluck('uuid');

        $hasAlignment = ActionObjectiveAlignment::where('objective_uuid', $objective->uuid)->exists();
        $failed = !$hasAlignment;

        $alert = false;

        if ($indicatorUuids->isNotEmpty()) {
            // Periods ending in ≤ 7 days without any control
            $nearDeadline = IndicatorPeriod::whereIn('indicator_uuid', $indicatorUuids)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '<=', $next7Days)
                ->whereDoesntHave('controls')
                ->exists();

            // no strict
            // $nearDeadline = IndicatorPeriod::whereIn('indicator_uuid', $indicatorUuids)
            //     ->whereDate('end_date', '<=', $next7Days)
            //     ->whereDoesntHave('controls')
            //     ->exists();

            // Periods already overdue without any control
            $overdue = IndicatorPeriod::whereIn('indicator_uuid', $indicatorUuids)
                ->whereDate('end_date', '<', $today)
                ->whereDoesntHave('controls')
                ->exists();

            // Objective expired but some indicator periods are still not controlled
            $expiredWithMissingControls = $objective->end_date <= $today &&
                IndicatorPeriod::whereIn('indicator_uuid', $indicatorUuids)
                ->whereDoesntHave('controls')
                ->exists();

            $alert = $nearDeadline || $overdue || $expiredWithMissingControls;
        }

        $objective->updateQuietly([
            'failed' => $failed,
            'alert' => $alert,
        ]);
    }
}
