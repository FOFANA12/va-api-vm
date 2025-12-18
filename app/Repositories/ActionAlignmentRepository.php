<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Http\Requests\ActionAlignmentRequest;
use App\Jobs\EvaluateActionJob;
use App\Jobs\UpdateActionAlignmentMetricsJob;
use App\Jobs\UpdateStructureAlignmentMetricsJob;
use App\Models\Action;
use App\Models\ActionObjectiveAlignment;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActionAlignmentRepository
{
    /**
     * List strategic objectives with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'name', 'lead_structure', 'structure'];
        $sortable = ['reference', 'name', 'priority', 'risk_level', 'lead_structure', 'structure', 'end_date', 'start_date', 'status'];

        $actionId   = $request->input('actionId');
        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = StrategicObjective::join('action_objective_alignments', 'strategic_objectives.uuid', '=', 'action_objective_alignments.objective_uuid')
            ->join('actions', 'action_objective_alignments.action_uuid', '=', 'actions.uuid')
            ->join('structures as lst', 'strategic_objectives.lead_structure_uuid', '=', 'lst.uuid')
            ->join('structures as st', 'strategic_objectives.structure_uuid', '=', 'st.uuid')
            ->select(
                'action_objective_alignments.id as id',
                'strategic_objectives.id as obj_id',
                'strategic_objectives.uuid',
                'strategic_objectives.reference',
                'strategic_objectives.name',
                'strategic_objectives.priority',
                'strategic_objectives.risk_level',
                'strategic_objectives.end_date',
                'strategic_objectives.start_date',
                'lst.name as lead_structure',
                'st.name as structure',
                'strategic_objectives.status',
                'strategic_objectives.state',
            )
            ->where('actions.id', $actionId);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'lead_structure') {
                        $q->orWhere('lst.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'structure') {
                        $q->orWhere('st.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("strategic_objectives.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'lead_structure') {
            $query->orderBy('lst.name', $sortOrder);
        } else if ($sortBy === 'structure') {
            $query->orderBy('st.name', $sortOrder);
        } else {
            $query->orderBy("strategic_objectives.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements(Action $action)
    {
        $alreadyAlignedObjectiveUuids = $action->objectives()->pluck('strategic_objectives.uuid')->toArray();

        $structure = $action->structure;
        $department = $structure->parent;

        $actionStart = $action->start_date ? Carbon::parse($action->start_date) : null;
        $actionEnd = $action->end_date ? Carbon::parse($action->end_date) : null;
        $hasActionDates = $actionStart && $actionEnd;

        $targetUuids = collect();

        if ($department && $department->type != 'STATE') {
            $targetUuids->push($department->uuid);
        }

        $targetUuids = $targetUuids->merge(
            Structure::where('type', 'STATE')->pluck('uuid')
        );

        $structures = Structure::whereIn('uuid', $targetUuids)
            ->with(['strategicMaps' => function ($q) {
                $q->where('status', true)
                    ->with(['elements.objectives', 'elements.parent']);
            }])
            ->get();

        $structuresOutput = $structures->map(function ($struc) use (
            $alreadyAlignedObjectiveUuids,
            $actionStart,
            $actionEnd,
            $hasActionDates
        ) {
            return [
                'uuid' => $struc->uuid,
                'name' => $struc->name,
                'type' => $struc->type,

                'strategic_maps' => $struc->strategicMaps->map(function ($map) use (
                    $alreadyAlignedObjectiveUuids,
                    $actionStart,
                    $actionEnd,
                    $hasActionDates
                ) {
                    return [
                        'uuid' => $map->uuid,
                        'name' => $map->name,

                        'elements' => $map->elements->sortBy('order')->values()->map(function ($elt) use (
                            $alreadyAlignedObjectiveUuids,
                            $actionStart,
                            $actionEnd,
                            $hasActionDates
                        ) {
                            $parent = $elt->parent
                                ? [
                                    'uuid' => $elt->parent->uuid,
                                    'name' => $elt->parent->name,
                                ]
                                : null;

                            if (!$hasActionDates) {
                                return [
                                    'uuid'      => $elt->uuid,
                                    'name'      => $elt->name,
                                    'parent'    => $parent,
                                    'objectives' => [],
                                ];
                            }

                            $eligibleObjectives = $elt->objectives->filter(function ($objective) use ($actionStart, $actionEnd) {
                                if (!$objective->start_date || !$objective->end_date) {
                                    return false;
                                }

                                $start = Carbon::parse($objective->start_date);
                                $end = Carbon::parse($objective->end_date);

                                return $start->between($actionStart, $actionEnd)
                                    || $end->between($actionStart, $actionEnd);
                            });

                            return [
                                'uuid' => $elt->uuid,
                                'name' => $elt->name,
                                'parent' => $parent,
                                'objectives' => $eligibleObjectives->map(function ($objective) use ($alreadyAlignedObjectiveUuids) {
                                    return [
                                        'uuid' => $objective->uuid,
                                        'reference' => $objective->reference,
                                        'name' => $objective->name,
                                        'start_date' => DateTimeFormatter::formatDate($objective->start_date),
                                        'end_date'   => DateTimeFormatter::formatDate($objective->end_date),
                                        'status'     => StrategicObjectiveStatus::get($objective->status, app()->getLocale()),
                                        'state'      => StrategicState::get($objective->state, app()->getLocale()),
                                        'is_aligned' => in_array($objective->uuid, $alreadyAlignedObjectiveUuids),
                                    ];
                                })->values(),
                            ];
                        }),
                    ];
                }),
            ];
        });

        return [
            'structures' => $structuresOutput,
        ];
    }

    /**
     * Align objectives to action.
     */
    public function align(ActionAlignmentRequest $request, Action $action)
    {
        DB::beginTransaction();
        try {
            $userUuid = Auth::user()?->uuid;
            $alignedCount = 0;

            foreach ($request->input('lines', []) as $line) {
                $objectiveUuid = $line['objective'];
                $identifier = $action->uuid . '-' . $objectiveUuid;
                $objective = StrategicObjective::where('uuid', $objectiveUuid)->first();

                $exists = ActionObjectiveAlignment::where('identifier', $identifier)
                    ->exists();

                if (!$exists) {
                    ActionObjectiveAlignment::create([
                        'identifier' => $identifier,
                        'action_structure_uuid' => $action->structure_uuid,
                        'action_uuid' => $action->uuid,
                        'objective_structure_uuid' => $objective->structure_uuid,
                        'strategic_map_uuid' => $objective->strategic_map_uuid,
                        'strategic_element_uuid' => $objective->strategic_element_uuid,
                        'objective_uuid' => $objectiveUuid,
                        'aligned_by' => $userUuid,
                        'aligned_at' => now(),
                    ]);

                    $alignedCount++;
                }
            }

            dispatch(new UpdateActionAlignmentMetricsJob($action->uuid));
            dispatch(new UpdateStructureAlignmentMetricsJob($action->structure_uuid));
            dispatch(new EvaluateActionJob($action->uuid));

            DB::commit();

            return $alignedCount;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Unalign (delete) one or more objectives from an action.
     */
    public function unalign(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $alignments = ActionObjectiveAlignment::whereIn('id', $ids)->get();

            if ($alignments->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $actionUuids = $alignments->pluck('action_uuid')->unique();
            $structureUuids = $alignments->pluck('action_structure_uuid')->unique();

            ActionObjectiveAlignment::whereIn('id', $ids)->delete();

            DB::commit();

            foreach ($actionUuids as $actionUuid) {
                dispatch(new UpdateActionAlignmentMetricsJob($actionUuid));
                dispatch(new EvaluateActionJob($actionUuid));
            }

            foreach ($structureUuids as $structureUuid) {
                dispatch(new UpdateStructureAlignmentMetricsJob($structureUuid));
            }
        } catch (\RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
