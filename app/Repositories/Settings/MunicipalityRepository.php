<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\MunicipalityRequest;
use App\Http\Resources\Settings\MunicipalityResource;
use App\Models\Region;

class MunicipalityRepository
{
    /**
     * List municipalities with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['municipalities.name', 'department', 'region'];
        $sortable = ['name', 'latitude', 'longitude', 'status', 'department', 'region'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Municipality::select(
            'municipalities.id',
            'municipalities.uuid',
            'municipalities.name',
            'municipalities.latitude',
            'municipalities.longitude',
            'municipalities.status',
            'departments.name as department',
            'regions.name as region',
            'municipalities.created_by',
        )
            ->join('departments', 'municipalities.department_uuid', '=', 'departments.uuid')
            ->join('regions', 'departments.region_uuid', '=', 'regions.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'department') {
                        $q->orWhere('departments.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'region') {
                        $q->orWhere('regions.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'department') {
            $query->orderBy('departments.name', $sortOrder);
        } else if ($sortBy === 'region') {
            $query->orderBy('regions.name', $sortOrder);
        } else {
            $query->orderBy("municipalities.$sortBy", $sortOrder);
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
        $regions = Region::with(
            ['departments' => function ($query) {
                $query->where('status', true)->select('uuid', 'name', 'region_uuid');
            }]
        )
            ->where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        return [
            'regions' => $regions,
        ];
    }

    /**
     * Store a new municipality.
     */
    public function store(MunicipalityRequest $request)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'view'),
            "department_uuid" => $request->input('department'),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $municipality = Municipality::create($request->only([
            "name",
            "department_uuid",
            "latitude",
            "longitude",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new MunicipalityResource($municipality);
    }

    /**
     * Show a specific municipality.
     */
    public function show(Municipality $municipality)
    {
        return ['municipality' => new MunicipalityResource($municipality)];
    }

    /**
     * Update an municipality.
     */
    public function update(MunicipalityRequest $request, Municipality $municipality)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'edit'),
            "department_uuid" => $request->input('department'),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $municipality->fill($request->only([
            'name',
            'department_uuid',
            'latitude',
            'longitude',
            'status',
            'updated_by',
        ]))->save();

        return new MunicipalityResource($municipality);
    }

    /**
     * Delete multiple funding sources.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = Municipality::whereIn('id', $ids)->delete();
                if ($deleted === 0) {
                    throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
                }
            });
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            if ($e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
