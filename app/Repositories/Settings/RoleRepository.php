<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\RoleRequest;
use App\Http\Resources\Settings\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RoleRepository
{
    /**
     * List roles with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name',];
        $sortable = ['name'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Role::query();

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }

        $query->orderBy($sortBy, $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Return grouped permissions for UI display.
     */
    public function requirements(): array
    {
        $permissions = Permission::select('id', 'uuid', 'name', 'category', 'description')->get();

        $grouped = $permissions->groupBy('category')->map(fn($group) => $group->values())->toArray();

        return ['permissions' => $grouped];
    }

    /**
     * Create a new role.
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'mode' => $request->input('mode', 'view'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $role = Role::create($request->only([
                'name',
                'created_by',
                'updated_by'
            ]));

            $permissions = $request->input('permissions', []);
            if (!empty($permissions)) {
                $role->permissions()->sync($permissions);
            }

            $role->load(['permissions']);

            DB::commit();
            return new RoleResource($role);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Show a specific role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions']);
        return ['role' => new RoleResource($role)];
    }

    /**
     * Update an existing role.
     */
    public function update(RoleRequest $request, Role $role)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'mode' => $request->input('mode', 'edit'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $role->fill($request->only([
                'name',
                'updated_by'
            ]))->save();

            $permissions = $request->input('permissions', []);
            $role->permissions()->sync($permissions);

            $role->load('permissions');

            DB::commit();

            return new RoleResource($role);
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete multiple currencies.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Role::whereIn('id', $ids)->delete();

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
