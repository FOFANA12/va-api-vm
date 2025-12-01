<?php

namespace App\Jobs;

use App\Models\Action;
use App\Models\ActionControl;
use App\Models\ActionMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateActionProgressJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $actionUuid;

    public function __construct(string $actionUuid)
    {
        $this->actionUuid = $actionUuid;
    }

    public function handle(): void
    {
        $action = Action::where('uuid', $this->actionUuid)->first();
        if (!$action) {
            return;
        }

        $lastControl = ActionControl::whereHas('actionPeriod', function ($q) use ($action) {
            $q->where('action_uuid', $action->uuid);
        })
            ->orderByDesc('control_date')
            ->orderByDesc('id')
            ->first();

        $state = 'none';
        $progress = $lastControl?->actual_progress_percent ?? 0;
        $realizationIndex = $lastControl && $lastControl->forecast_percent > 0
            ? round($lastControl->actual_progress_percent / $lastControl->forecast_percent, 2)
            : 0;

        if ($lastControl) {
            $period = $lastControl->actionPeriod;
            $target = $lastControl->forecast_percent ?? 0;

            if ($period && $period->isLast()) {
                $state = $progress < $target ? 'risk' : 'achieved';
            } else {
                if ($progress < ($target * 0.25)) {
                    $state = 'risk';
                } elseif ($progress < $target) {
                    $state = 'delayed';
                } else {
                    $state = 'on_track';
                }
            }
        }

        $action->update([
            'state' => $state,
            'actual_progress_percent' => $progress,
        ]);

        // 👉 update ou insert metrics
        ActionMetric::updateOrCreate(
            ['action_uuid' => $action->uuid, 'action_id' => $action->id],
            [
                'realization_rate' => $progress,
                'realization_index' => $realizationIndex,
            ]
        );
    }
}
