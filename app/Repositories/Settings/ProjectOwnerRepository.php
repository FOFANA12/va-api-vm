<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\ProjectOwnerRequest;
use App\Http\Resources\Settings\ProjectOwnerResource;
use App\Models\ProjectOwner;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectOwnerRepository
{
    /**
     * List owners with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['project_owners.name', 'project_owners.type', 'project_owners.phone', 'structure'];
        $sortable = ['name', 'status', 'phone', 'type', 'structure'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'project_owners.id';

        $query = ProjectOwner::select(
            'project_owners.id',
            'project_owners.uuid',
            'project_owners.structure_uuid',
            'project_owners.name',
            'project_owners.type',
            'project_owners.email',
            'project_owners.phone',
            'project_owners.status',
            'structures.name as structure'
        )
        ->leftJoin('structures', 'project_owners.structure_uuid', '=', 'structures.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif($column === 'name'){
                        $q->onWhere('project_owners.name', 'LIKE', '%' . strtolower($searchTerm). '%');
                    } elseif($column === 'phone'){
                        $q->onWhere('project_owners.phone', 'LIKE', '%' . strtolower($searchTerm). '%');
                    } elseif($column === 'type'){
                        $q->onWhere('project_owners.type', 'LIKE', '%' . strtolower($searchTerm). '%');
                    }
                     else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }
        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data for forms.
     */
    public function requirements()
    {
        $structures = Structure::select('uuid', 'name')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return [
            'structures' => $structures
        ];
    }

    /**
     * Store a newly created owner.
     */
    public function store(ProjectOwnerRequest $request)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $projectOwner = ProjectOwner::create($request->only([
            'structure_uuid',
            'name',
            'type',
            'email',
            'phone',
            'status',
            'created_by',
            'updated_by'
        ]));

        return new ProjectOwnerResource($projectOwner);
    }

    /**
     * Show details of an owner.
     */
     
    public function show(ProjectOwner $projectOwner)
    {
        return ['project_owner' => new ProjectOwnerResource($projectOwner->loadMissing('structure'))];
    }


    /**
     * Update an existing owner.
     */
    public function update(ProjectOwnerRequest $request, ProjectOwner $projectOwner)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'edit'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $projectOwner->fill($request->only([
            'structure_uuid',
            'name',
            'type',
            'email',
            'phone',
            'status',
            'updated_by'
        ]))->save();

        return new ProjectOwnerResource($projectOwner);
    }

    /**
     * Delete one or multiple owners.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = ProjectOwner::whereIn('id', $ids)->delete();
            if ($deleted === 0) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
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
