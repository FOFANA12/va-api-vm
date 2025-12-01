<?php

namespace App\Jobs;

use App\Models\Structure;
use App\Models\ActionObjectiveAlignment;
use App\Models\StructureMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStructureAlignmentMetricsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $structureUuid;

    public function __construct(string $structureUuid)
    {
        $this->structureUuid = $structureUuid;
    }

    public function handle(): void
    {
        $structure = Structure::where('uuid', $this->structureUuid)->first();
        if (!$structure) {
            return;
        }

        $alignments = ActionObjectiveAlignment::where('action_structure_uuid', $structure->uuid)
            ->get();

        $alignedMapsCount = $alignments->pluck('objective.strategic_map_uuid')->filter()->unique()->count();
        $alignedAxesCount = $alignments->pluck('objective.strategic_axis_uuid')->filter()->unique()->count();
        $alignedObjectivesCount = $alignments->pluck('objective_uuid')->unique()->count();

        StructureMetric::updateOrCreate(
            ['structure_uuid' => $structure->uuid],
            [
                'aligned_maps_count' => $alignedMapsCount,
                'aligned_axes_count' => $alignedAxesCount,
                'aligned_objectives_count' => $alignedObjectivesCount,
            ]
        );
    }
}
