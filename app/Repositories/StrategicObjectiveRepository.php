<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Helpers\ReferenceGenerator;
use App\Http\Requests\StrategicObjectiveRequest;
use App\Http\Resources\StrategicObjectiveResource;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\StrategicElement;
use App\Models\StrategicMap;
use App\Models\Structure;
use App\Models\StrategicObjective;
use App\Services\StructureAccessService;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use App\Support\StrategicObjectiveStatus;
use App\Support\StrategicState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class StrategicObjectiveRepository
{
    /**
     * Injected service for structure visibility.
     */
    protected StructureAccessService $structureAccess;

    public function __construct(StructureAccessService $structureAccess)
    {
        $this->structureAccess = $structureAccess;
    }

    /**
     * List strategic objective  with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'name', 'lead_structure'];
        $sortable = ['reference', 'name', 'priority', 'risk_level', 'lead_structure', 'structure', 'end_date', 'start_date', 'status', 'state'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = StrategicObjective::join('structures as lst', 'strategic_objectives.lead_structure_uuid', '=', 'lst.uuid')
            ->join('structures as st', 'strategic_objectives.structure_uuid', '=', 'st.uuid')
            ->select(
                'strategic_objectives.id as id',
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
            );

        $allowed = $this->structureAccess->getAccessibleStructureUuids(Auth::user());
        if ($allowed !== null) {
            $query->whereIn('strategic_objectives.structure_uuid', $allowed);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('strategic_objectives.status', $request->status);
        }

        // State filter
        if ($request->filled('state')) {
            $query->where('strategic_objectives.state', $request->state);
        }

        // Nature filter (alert or failed)
        if ($request->filled('nature')) {
            if ($request->nature === 'alert') {
                $query->where('strategic_objectives.alert', true);
            } elseif ($request->nature === 'failed') {
                $query->where('strategic_objectives.failed', true);
            }
        }

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('st.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'lead_structure') {
                        $q->orWhere('lst.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
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

        $query->orderBy($sortBy, $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements(Request $request)
    {
        if ($request->mode == 'filters') {
            $statuses =  collect(StrategicObjectiveStatus::all())->map(function ($item) {
                return [
                    'code' => $item['code'],
                    'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
                ];
            });

            $states =  collect(StrategicState::all())->map(function ($item) {
                return [
                    'code' => $item['code'],
                    'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
                ];
            });

            return [
                'statuses' => $statuses,
                'states' => $states,
            ];
        }

        $ownerStructures = Structure::query()
            ->where('status', true)
            ->whereIn('type', ['STATE', 'STRATEGIC'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'type')
            ->get();

        $leadStructures = Structure::query()
            ->where('status', true)
            ->whereIn('type', ['STRATEGIC', 'OPERATIONAL', 'VIRTUAL'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'type')
            ->get();

        $strategicMaps = StrategicMap::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'structure_uuid')
            ->get();

        $strategicElements = StrategicElement::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'strategic_map_uuid', 'type')
            ->get();

        $priorityLevels =  collect(PriorityLevel::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'color' => $item['color'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $riskLevels =  collect(RiskLevel::all())->map(function ($item) {
            return [
                'color' => $item['color'],
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return [
            'structures' => $ownerStructures,
            'lead_structures' => $leadStructures,
            'strategic_maps' => $strategicMaps,
            'strategic_elements' => $strategicElements,
            'risk_levels' => $riskLevels,
            'priority_levels' => $priorityLevels,
        ];
    }

    /**
     * Store a new strategic objective.
     */
    public function store(StrategicObjectiveRequest $request)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'strategic_map_uuid' => $request->input('strategic_map'),
            'strategic_element_uuid' => $request->input('strategic_element'),
            'lead_structure_uuid' => $request->input('lead_structure'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
            'status_changed_at' => now(),
            'status_changed_by' => Auth::user()?->uuid,
        ]);

        $strategicObjective = StrategicObjective::create($request->only([
            'name',
            'structure_uuid',
            'lead_structure_uuid',
            'strategic_map_uuid',
            'strategic_element_uuid',
            'start_date',
            'end_date',
            'description',
            'priority',
            'risk_level',
            'created_by',
            'updated_by'
        ]));

        $strategicObjective->update([
            'reference' => ReferenceGenerator::generateStrategicObjectiveReference(
                $strategicObjective->id,
                $strategicObjective->structure->abbreviation,
                $strategicObjective->strategicElement->abbreviation
            ),
        ]);

        $strategicObjective->loadMissing([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'statusChangedBy',
        ]);

        $strategicObjective->refresh();

        return (new StrategicObjectiveResource($strategicObjective))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Show a specific strategic objective.
     */
    public function show(StrategicObjective $strategicObjective)
    {
        $strategicObjective->loadMissing([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'statusChangedBy',
        ]);

        return ['strategic_objective' => new StrategicObjectiveResource($strategicObjective)];
    }

    /**
     * Update an strategic objective.
     */
    public function update(StrategicObjectiveRequest $request, StrategicObjective $strategicObjective)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'strategic_map_uuid' => $request->input('strategic_map'),
            'strategic_element_uuid' => $request->input('strategic_element'),
            'lead_structure_uuid' => $request->input('lead_structure'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $strategicObjective->fill($request->only([
            'name',
            'structure_uuid',
            'lead_structure_uuid',
            'strategic_map_uuid',
            'strategic_element_uuid',
            'start_date',
            'end_date',
            'description',
            'priority',
            'risk_level',
            'updated_by'
        ]));

        $strategicObjective->save();
        dispatch(new EvaluateStrategicObjectiveJob($strategicObjective->uuid));

        $strategicObjective->loadMissing([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'statusChangedBy',
        ]);


        return (new StrategicObjectiveResource($strategicObjective))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Retrieve available strategic objective statuses with localized labels.
     */
    public function getStatuses(StrategicObjective $strategicObjective)
    {
        $current = $strategicObjective->status;
        $next = StrategicObjectiveStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = StrategicObjectiveStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Update the status of a specific strategic objective.
     */
    public function updateStatus(Request $request, StrategicObjective $strategicObjective)
    {
        $strategicObjective->status = $request->input('status');
        $strategicObjective->status_changed_at = now();
        $strategicObjective->status_changed_by = Auth::user()?->uuid;
        $strategicObjective->timestamps = false;
        $strategicObjective->save();

        return [
            'status' => StrategicObjectiveStatus::get($strategicObjective->status, app()->getLocale()),
            'status_changed_at' => $strategicObjective->status_changed_at ? DateTimeFormatter::formatDatetime($strategicObjective->status_changed_at) : null,
            'status_changed_by' => $strategicObjective->statusChangedBy?->name,
        ];
    }

    /**
     * Delete strategic objective(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = StrategicObjective::whereIn('id', $ids)->delete();
            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }


            DB::commit();
        } catch (RuntimeException $e) {
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
