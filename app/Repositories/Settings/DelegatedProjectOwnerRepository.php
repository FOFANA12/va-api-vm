<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\DelegatedProjectOwnerRequest;
use App\Http\Resources\Settings\DelegatedProjectOwnerResource;
use App\Models\DelegatedProjectOwner;
use App\Models\ProjectOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DelegatedProjectOwnerRepository
{
    /**
     * List delegated project owners with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['delegated_project_owners.name', 'delegated_project_owners.email', 'delegated_project_owners.phone', 'project_owner'];
        $sortable = ['delegated_project_owners.name', 'delegated_project_owners.email', 'delegated_project_owners.phone',
         'delegated_project_owners.status', 'project_owner'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'delegated_project_owners.id';

        $query = DelegatedProjectOwner::select(
            'delegated_project_owners.id',
            'delegated_project_owners.uuid',
            'delegated_project_owners.project_owner_uuid',
            'delegated_project_owners.name',
            'delegated_project_owners.email',
            'delegated_project_owners.phone',
            'delegated_project_owners.status',
            'project_owners.name as project_owner'
        )
        ->leftJoin('project_owners', 'delegated_project_owners.project_owner_uuid', '=', 'project_owners.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'project_owner') {
                        $q->orWhere('project_owners.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif($column === 'name'){
                        $q->onWhere('delegated_project_owners.name', 'LIKE', '%' . strtolower($searchTerm). '%');
                    } elseif($column === 'phone'){
                        $q->onWhere('delegated_project_owners.phone', 'LIKE', '%' . strtolower($searchTerm). '%');
                    }
                     else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        $query->orderBy($sortBy, $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data for forms.
     */
    public function requirements()
    {
        $projectOwners = ProjectOwner::select('uuid', 'name')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return [
            'project_owners' => $projectOwners
        ];
    }

    /**
     * Store a newly created delegated project owner.
     */
    public function store(DelegatedProjectOwnerRequest $request)
    {
        $request->merge([
            'project_owner_uuid' => $request->input('project_owner'),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $owner = DelegatedProjectOwner::create($request->only([
            'project_owner_uuid',
            'name',
            'email',
            'phone',
            'status',
            'created_by',
            'updated_by'
        ]));

        return new DelegatedProjectOwnerResource($owner);
    }

    /**
     * Show details of a delegated project owner.
     */
    public function show(DelegatedProjectOwner $delegated_project_owner)
    {
        return [
            'delegated_project_owner' => new DelegatedProjectOwnerResource(
                $delegated_project_owner->loadMissing('projectOwner')
            )
        ];
    }

    /**
     * Update an existing delegated project owner.
     */
    public function update(DelegatedProjectOwnerRequest $request, DelegatedProjectOwner $delegated_project_owner)
    {
        $request->merge([
            'project_owner_uuid' => $request->input('project_owner'),
            'mode' => $request->input('mode', 'edit'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $delegated_project_owner->fill($request->only([
            'project_owner_uuid',
            'name',
            'email',
            'phone',
            'status',
            'updated_by'
        ]))->save();

        return new DelegatedProjectOwnerResource($delegated_project_owner);
    }

    /**
     * Delete one or multiple delegated project owners.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = DelegatedProjectOwner::whereIn('id', $ids)->delete();
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
