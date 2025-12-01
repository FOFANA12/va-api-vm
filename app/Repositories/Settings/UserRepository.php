<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\UserRequest;
use App\Http\Resources\Settings\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserRepository
{
    /**
     * List users with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'email', 'phone', 'lang'];
        $sortable = ['name', 'email', 'phone', 'lang', 'status'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = User::select(
            'users.id',
            'users.uuid',
            'users.name',
            'email',
            'phone',
            'lang',
            'status',
            'roles.name as role',
        )
            ->join('roles', 'users.role_uuid', '=', 'roles.uuid')
            ->where('users.id', '<>', Auth::id())
            ->whereDoesntHave('employee')
            ->newQuery();

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'role') {
                        $q->orWhere("roles.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                    $q->orWhere("users.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }

        if($sortBy === 'role'){
            
            $query->orderBy("roles.$sortBy", $sortOrder);
        }
        $query->orderBy("users.$sortBy", $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }


    /**
     * Load requirements data
     */
    public function requirements()
    {
        $roles = Role::orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        return [
            'roles' => $roles,
        ];
    }

    /**
     * Store a new user.
     */
    public function store(UserRequest $request)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'password' => Hash::make($request->password),
            'role_uuid' => $request->input('role'),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user() ? Auth::user()->uuid : null,
            'updated_by' => Auth::user() ? Auth::user()->uuid : null,
        ]);

        $user = User::create($request->only([
            'name',
            'email',
            'role_uuid',
            'phone',
            'lang',
            'password',
            'status'
        ]));

        $user->load(['role']);
        return new UserResource($user);
    }

    /**
     * Show a specific user.
     */
    public function show(User $user)
    {
        return ['user' => new UserResource($user->load('role'))];
    }

    /**
     * Update a user.
     */
    public function update(UserRequest $request, User $user)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'role_uuid' => $request->input('role'),
            'mode' => $request->input('mode', 'edit'),
        ]);

        $user->fill($request->only([
            'name',
            'role_uuid',
            'email',
            'phone',
            'lang',
            'status',
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        $user->load(['role']);

        return new UserResource($user);
    }

    /**
     * Delete users.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = User::whereIn('id', $ids)->delete();
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
