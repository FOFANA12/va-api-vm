<?php

namespace App\Repositories\Report;

use App\Models\Action;
use App\Models\ActionMetric;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;

class DashboardReportRepository
{
    public function getGeneralDahsboard(Structure $structure): array
    {
        $structures = [];
        $collect = function ($s) use (&$structures, &$collect) {
            foreach ($s->children as $child) {
                if ($child->status) {
                    $structures[] = $child->uuid;
                }
                $collect($child);
            }
        };
        $collect($structure);

        $structureUuids = collect($structures)->pluck('uuid');
        $structureCount = count($structureUuids);

        $strategicMaps = StrategicMap::with(['elements.objectives'])
            ->whereIn('structure_uuid', $structureUuids)
            ->where('status', true)
            ->get();

        $structureMapsCount = $strategicMaps->count();
        $strategicAxesCount = $strategicMaps->sum(fn($map) => $map->elements->count());

        $leadObjectivesCount = $strategicMaps->sum(
            fn($map) =>
            $map->objectives->where('lead_structure_uuid', $structure->uuid)->count()
        );

        $actions = Action::whereIn('structure_uuid', $structureUuids)->get();
        $actionsCount = $actions->count();
        $actionUuids = $actions->pluck('uuid');

        $metrics = ActionMetric::whereIn('action_uuid', $actionUuids)->get();

        $realizationRate = $metrics->avg('realization_rate') ?? 0;
        $realizationIndex = $metrics->avg('realization_index') ?? 0;

        $disbursementRates = $actions->map(function ($action) {
            if ($action->total_receipt_fund > 0) {
                return ($action->total_disbursement_fund / $action->total_receipt_fund) * 100;
            }
            return 0;
        });
        $disbursementRate = $disbursementRates->avg() ?? 0;

        $failedObjectivesCount = StrategicObjective::whereIn('structure_uuid', $structureUuids)
            ->where('failed', true)
            ->count();

        $alertObjectivesCount = StrategicObjective::whereIn('structure_uuid', $structureUuids)
            ->where('alert', true)
            ->count();

        $failedActionsCount = Action::whereIn('structure_uuid', $structureUuids)
            ->where('failed', true)
            ->count(); // action en vain == no alignment

        $alertActionsCount = Action::whereIn('structure_uuid', $structureUuids)
            ->where('alert', true)
            ->count();

        return [
            'structures_active_count' => $structureCount,
            'strategic_maps_active_count' => $structureMapsCount,
            'strategic_axes_count' => $strategicAxesCount,
            'lead_objectives_count' => $leadObjectivesCount,

            'actions_count' => $actionsCount,
            'realization_rate' => round($realizationRate, 2),
            'disbursement_rate' => round($disbursementRate, 2),
            'realization_index' => round($realizationIndex, 2),

            'failed_objectives_count' => $failedObjectivesCount,
            'alert_objectives_count' => $alertObjectivesCount,

            'failed_actions_count' => $failedActionsCount,
            'alert_actions_count' => $alertActionsCount,
        ];
    }
}
