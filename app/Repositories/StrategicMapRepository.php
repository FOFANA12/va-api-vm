<?php

namespace App\Repositories;

use RuntimeException;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StrategicMap;
use App\Http\Requests\StrategicMapRequest;
use App\Http\Resources\StrategicMapResource;

class StrategicMapRepository
{
    /**
     * List strategic map with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'structure'];
        $sortable = ['name', 'start_date', 'end_date', 'status', 'structure'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = StrategicMap::select(
            'strategic_maps.id',
            'strategic_maps.uuid',
            'strategic_maps.name',
            'strategic_maps.description',
            'strategic_maps.start_date',
            'strategic_maps.end_date',
            'strategic_maps.status',
            'structures.name as structure',
        )
            ->join('structures', 'strategic_maps.structure_uuid', '=', 'structures.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("strategic_maps.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else {
            $query->orderBy("strategic_maps.$sortBy", $sortOrder);
        }

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

        return [
            'structures' => $structures,
        ];
    }

    /**
     * Create a new strategic map.
     */
    public function store(StrategicMapRequest $request)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "structure_uuid" => $request->input('structure'),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $strategicMap = StrategicMap::create($request->only([
            "name",
            "structure_uuid",
            "description",
            "start_date",
            "end_date",
            "status",
            "created_by",
            "updated_by",
        ]));

        $strategicMap->load('structure');
        return (new StrategicMapResource($strategicMap))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Show a specific strategic map.
     */
    public function show(Request $request, StrategicMap $strategicMap)
    {
        $mode = $request->input('mode', 'view');

        $relations = ['structure'];

        if ($mode === 'details') {
            $relations[] = 'elements.objectives';
        }

        $strategicMap->load($relations);

        if ($strategicMap->relationLoaded('elements')) {
            $strategicMap->setRelation(
                'elements',
                $strategicMap->elements->sortBy('order')->values()
            );

            if ($mode === 'details') {
                foreach ($strategicMap->elements as $element) {
                    if ($element->relationLoaded('objectives')) {
                        $element->setRelation(
                            'objectives',
                            $element->objectives->sortBy('reference')->values()
                        );
                    }
                }
            }
        }

        return ['strategic_map' => new StrategicMapResource($strategicMap)];
    }

    /**
     * Update a strategic map.
     */
    public function update(StrategicMapRequest $request, StrategicMap $strategicMap)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "structure_uuid" => $request->input('structure'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $strategicMap->fill($request->only([
            "name",
            "structure_uuid",
            "description",
            "start_date",
            "end_date",
            "status",
            "updated_by",
        ]))->save();

        $strategicMap->loadMissing('structure');

        return (new StrategicMapResource($strategicMap))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Delete multiple strategic map.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = StrategicMap::whereIn('id', $ids)->delete();
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
