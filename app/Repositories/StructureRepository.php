<?php

namespace App\Repositories;

use App\Http\Requests\StructureRequest;
use App\Http\Resources\StructureResource;
use App\Models\Structure;
use App\Support\StructureType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StructureRepository
{
    /**
     * List structures with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['structures.name', 'structures.abbreviation', 'parent'];
        $sortable = ['name', 'abbreviation', 'status', 'parent', 'type'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Structure::select(
            'structures.id',
            'structures.uuid',
            'structures.abbreviation',
            'structures.name',
            'structures.parent_uuid',
            'structures.status',
            'structures.type',
            'parents.name as parent'
        )
            ->leftJoin('structures as parents', 'structures.parent_uuid', '=', 'parents.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'parent') {
                        $q->orWhere('parents.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'parent') {
            $query->orderBy('parents.name', $sortOrder);
        } else {
            $query->orderBy("structures.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements(Request $request)
    {
        $excludedIds = $request->input('exclude', []);

        $structures = Structure::select('uuid', 'name', 'abbreviation', 'type')
            ->where('status', true)
            ->when(!empty($excludedIds), function ($query) use ($excludedIds) {
                $query->whereNotIn('id', $excludedIds);
            })
            ->orderBy('id', 'desc')
            ->get();

        $types =  collect(StructureType::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return ['structures' => $structures, 'types' => $types];
    }

    /**
     * Create a new structure.
     */
    public function store(StructureRequest $request)
    {
        $request->merge([
            'parent_uuid' => $request->input('parent'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $structure = Structure::create($request->only([
            'abbreviation',
            'name',
            'parent_uuid',
            'status',
            'type',
            'created_by',
            'updated_by'
        ]));

        return new StructureResource($structure);
    }

    /**
     * Show a specific structure.
     */
    public function show(Structure $structure)
    {
        return ['structure' => new StructureResource($structure->loadMissing('parent'))];
    }

    /**
     * Update a structure.
     */
    public function update(StructureRequest $request, Structure $structure)
    {
        $request->merge([
            'parent_uuid' => $request->input('parent'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'edit'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $structure->fill($request->only([
            'abbreviation',
            'name',
            'parent_uuid',
            'status',
            'updated_by'
        ]))->save();

        return new StructureResource($structure);
    }

    /**
     * Delete multiple structures.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Structure::whereIn('id', $ids)->delete();
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
