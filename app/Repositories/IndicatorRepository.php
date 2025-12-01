<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Helpers\ReferenceGenerator;
use App\Http\Requests\IndicatorRequest;
use App\Http\Resources\IndicatorResource;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\Indicator;
use App\Models\IndicatorCategory;
use App\Models\StrategicMap;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\ActionStatus;
use App\Support\ChartType;
use App\Support\FrequencyUnit;
use App\Support\IndicatorStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class IndicatorRepository
{
    /**
     * List indicators with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['lead_structure', 'structure', 'reference', 'chart_type', 'unit'];
        $sortable = ['lead_structure', 'structure', 'reference', 'chart_type', 'initial_value',  'final_target_value', 'achieved_value', 'status', 'state'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Indicator::join('structures as str', 'indicators.structure_uuid', '=', 'str.uuid')
            ->join('structures as strP', 'indicators.lead_structure_uuid', '=', 'strP.uuid')
            ->select(
                'indicators.id as id',
                'indicators.uuid',
                'strP.name as lead_structure',
                'str.name as structure',
                'indicators.reference',
                'indicators.chart_type',
                'indicators.initial_value',
                'indicators.final_target_value',
                'indicators.achieved_value',
                'indicators.unit',
                'indicators.status',
                'indicators.state',
                'indicators.is_planned',
            );


        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('str.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                    if ($column === 'lead_structure') {
                        $q->orWhere('strP.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("indicators.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('str.name', $sortOrder);
        } else if ($sortBy === 'lead_structure') {
            $query->orderBy('strP.name', $sortOrder);
        } else {
            $query->orderBy("indicators.$sortBy", $sortOrder);
        }

        $query->orderBy($sortBy, $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $structures = Structure::query()
            ->where('status', true)
            ->whereIn('type', ['STATE', 'DEPARTMENT'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'type')
            ->get();

        $strategicMaps = StrategicMap::where('status', true)
            ->select('uuid', 'name', 'structure_uuid')
            ->orderByDesc('id')
            ->with(['elements' => function ($q) {
                $q->where('status', true)
                    ->select('uuid', 'name', 'strategic_map_uuid')
                    ->orderByDesc('id')
                    ->with(['objectives' => function ($q2) {
                        $q2->select('uuid', 'name', 'start_date', 'end_date', 'strategic_element_uuid')
                            ->orderByDesc('id');
                    }]);
            }])
            ->get();


        $categories = IndicatorCategory::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();


        $frequencyUnits =  collect(FrequencyUnit::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $chartTypes =  collect(ChartType::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return [
            'structures' => $structures,
            'strategic_maps' => $strategicMaps,
            'categories' => $categories,
            'frequency_units' => $frequencyUnits,
            'chart_types' => $chartTypes,
        ];
    }

    /**
     * Store a new indicators.
     */
    public function store(IndicatorRequest $request)
    {

        $objective = StrategicObjective::where('uuid', $request->input('strategic_objective'))->firstOrFail();

        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'strategic_map_uuid' => $request->input('strategic_map'),
            'strategic_element_uuid' => $request->input('strategic_element'),
            'strategic_objective_uuid' => $request->input('strategic_objective'),
            'lead_structure_uuid' => $objective->lead_structure_uuid,
            'category_uuid' => $request->input('category'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
            'status_changed_at' => now(),
            'status_changed_by' => Auth::user()?->uuid,
        ]);

        $indicator = Indicator::create($request->only([
            'structure_uuid',
            'strategic_map_uuid',
            'strategic_element_uuid',
            'strategic_objective_uuid',
            'lead_structure_uuid',
            'category_uuid',
            'name',
            'description',
            'chart_type',
            'unit',
            'initial_value',
            'final_target_value',
            'created_by',
            'updated_by'
        ]));

        $indicator->update([
            'reference' => ReferenceGenerator::generateIndicatorReference($indicator->id, $objective->reference),
        ]);

        $indicator->load([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'strategicObjective',
            'category',
            'statusChangedBy',
        ]);
        $indicator->refresh();

        return (new IndicatorResource($indicator))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Show a specific indicator.
     */
    public function show(Indicator $indicator)
    {
        $indicator->load([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'strategicObjective',
            'category',
            'statusChangedBy',
        ]);

        return ['indicator' => new IndicatorResource($indicator)];
    }

    /**
     * Update an indicator.
     */
    public function update(IndicatorRequest $request, Indicator $indicator)
    {
        $request->merge([
            'category_uuid' => $request->input('category'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $indicator->fill($request->only([
            'category_uuid',
            'name',
            'description',
            'chart_type',
            'unit',
            'initial_value',
            'final_target_value',
            'updated_by'
        ]));

        $indicator->save();

        dispatch(new EvaluateStrategicObjectiveJob($indicator->strategic_objective_uuid));

        $indicator->load([
            'structure',
            'leadStructure',
            'strategicMap',
            'strategicElement',
            'strategicObjective',
            'category',
            'statusChangedBy',
        ]);

        return (new IndicatorResource($indicator))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Retrieve available indicator statuses with localized labels.
     */
    public function getStatuses()
    {
        return [
            'statuses' => collect(IndicatorStatus::all())->map(function ($item) {
                return [
                    'code' => $item['code'],
                    'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
                ];
            }),
        ];
    }

    /**
     * Update the status of a specific indicator.
     */
    public function updateStatus(Request $request, Indicator $indicator)
    {
        $status = $request->input('status');
        $indicator->status = $status;
        $indicator->status_changed_at = now();
        $indicator->status_changed_by = Auth::user()?->uuid;

        if ($status === 'initializing') {
            $indicator->actual_start_date = null;
            $indicator->actual_end_date = null;
        } elseif ($status === 'in_progress') {
            if (empty($indicator->actual_start_date)) {
                $indicator->actual_start_date = now();
            }
            $indicator->actual_end_date = null;
        } elseif ($status === 'paused') {
            $indicator->actual_end_date = null;
        } elseif ($status === 'completed') {
            $indicator->actual_end_date = now();
        }

        $indicator->timestamps = false;
        $indicator->save();

        dispatch(new EvaluateStrategicObjectiveJob($indicator->strategic_objective_uuid));

        return [
            'status' => ActionStatus::get($indicator->status, app()->getLocale()),
            'status_changed_at' => $indicator->status_changed_at ? DateTimeFormatter::formatDatetime($indicator->status_changed_at) : null,
            'status_changed_by' => $indicator->statusChangedBy?->name,
        ];
    }

    /**
     * Delete indicator(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $indicators = Indicator::whereIn('id', $ids)->get();

            if ($indicators->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $objectiveUuids = $indicators->pluck('strategic_objective_uuid')->unique();

            Indicator::whereIn('id', $ids)->delete();

            DB::commit();

            foreach ($objectiveUuids as $objectiveUuid) {
                dispatch(new EvaluateStrategicObjectiveJob($objectiveUuid));
            }
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
