<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\DepartmentRequest;
use App\Http\Resources\Settings\DepartmentResource;
use App\Models\Region;

class DepartmentRepository
{
    /**
     * List departments with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['departments.name', 'region'];
        $sortable = ['name', 'latitude', 'longitude', 'status', 'region'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Department::select(
            'departments.id',
            'departments.uuid',
            'departments.name',
            'departments.latitude',
            'departments.longitude',
            'departments.status',
            'regions.name as region',
            'departments.created_by',
        )
            ->join('regions', 'departments.region_uuid', '=', 'regions.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'region') {
                        $q->orWhere('regions.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'region') {
            $query->orderBy('regions.name', $sortOrder);
        } else {
            $query->orderBy("departments.$sortBy", $sortOrder);
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
        $regions = Region::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        return [
            'regions' => $regions,
        ];
    }

    /**
     * Store a new department.
     */
    public function store(DepartmentRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'view'),
            "region_uuid" => $request->input('region'),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $department = Department::create($request->only([
            "name",
            "region_uuid",
            "latitude",
            "longitude",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new DepartmentResource($department);
    }

    /**
     * Show a specific department.
     */
    public function show(Department $department)
    {
        return ['department' => new DepartmentResource($department)];
    }

    /**
     * Update an department.
     */
    public function update(DepartmentRequest $request, Department $department)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'edit'),
            "region_uuid" => $request->input('region'),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $department->fill($request->only([
            'name',
            'region_uuid',
            'latitude',
            'longitude',
            'status',
            'updated_by',
        ]))->save();

        return new DepartmentResource($department);
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
                $deleted = Department::whereIn('id', $ids)->delete();
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
