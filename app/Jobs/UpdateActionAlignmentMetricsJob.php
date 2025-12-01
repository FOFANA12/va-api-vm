<?php

namespace App\Jobs;

use App\Models\Action;
use App\Models\ActionObjectiveAlignment;
use App\Models\ActionMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateActionAlignmentMetricsJob implements ShouldQueue
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

        $alignments = ActionObjectiveAlignment::where('action_uuid', $action->uuid)
            ->get();

        $alignedMapsCount = $alignments->pluck('objective.strategic_map_uuid')->filter()->unique()->count();
        $alignedAxesCount = $alignments->pluck('objective.strategic_axis_uuid')->filter()->unique()->count();
        $alignedObjectivesCount = $alignments->pluck('objective_uuid')->unique()->count();

        ActionMetric::updateOrCreate(
            ['action_uuid' => $action->uuid, 'action_id' => $action->id],
            [
                'aligned_maps_count' => $alignedMapsCount,
                'aligned_axes_count' => $alignedAxesCount,
                'aligned_objectives_count' => $alignedObjectivesCount,
            ]
        );
    }
}
