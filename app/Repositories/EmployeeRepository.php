<?php

namespace App\Repositories;

use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class EmployeeRepository
{
    /**
     * List employees with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['job_title', 'name', 'email', 'phone', 'structure'];
        $sortable = ['job_title', 'name', 'email', 'phone', 'structure', 'status'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = User::join('employees', 'users.uuid', '=', 'employees.user_uuid')
            ->join('structures', 'employees.structure_uuid', '=', 'structures.uuid')
            ->select(
                'employees.id as id',
                'employees.uuid',
                'employees.job_title',
                'users.name as name',
                'users.email',
                'users.phone',
                'users.status',
                'structures.name as structure'
            )
            ->where('employees.user_uuid', '<>', Auth::user()->uuid);


        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'name' || $column === 'email' || $column === 'phone') {
                        $q->orWhere("users.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else if ($sortBy === 'name' || $sortBy === 'phone' || $sortBy === 'email') {
            $query->orderBy("users.$sortBy", $sortOrder);
        } else if ($sortBy === 'job_title') {
            $query->orderBy("employees.$sortBy", $sortOrder);
        } else {
            $query->orderBy("users.$sortBy", $sortOrder);
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
        $roles = Role::orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $structures = Structure::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'abbreviation')
            ->get();

        return [
            'roles' => $roles,
            'structures' => $structures,
        ];
    }

    /**
     * Store a new employee.
     */
    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'can_logged_in' => filter_var($request->input('can_logged_in'), FILTER_VALIDATE_BOOLEAN),
                'mode' => $request->input('mode', 'view'),
            ]);

            $employeeData = $request->only([
                'job_title',
                'floor',
                'office',
            ]);

            $employeeData['structure_uuid'] = $request->input('structure');

            $userData = [
                'name' => $request->input('name'),
                'role_uuid' => $request->input('role'),
                'email' => $request->input('email') ?: null,
                'phone' => $request->input('phone') ?: null,
                'lang' => $request->input('lang'),
                'status' => $request->input('status'),
            ];

            if ($request->can_logged_in && $request->filled('password')) {
                $userData['password'] = Hash::make($request->input('password'));
            }

            $user = User::create($userData);

            $employeeData['user_uuid'] = $user->uuid;
            $employeeData['can_logged_in'] = $request->can_logged_in;


            $employee = Employee::create($employeeData);
            $employee->load(['user', 'user.role', 'structure']);

            DB::commit();
            return new EmployeeResource($employee);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific employee.
     */
    public function show(Employee $employee)
    {
        return ['employee' => new EmployeeResource($employee->load('structure', 'user', 'user.role'))];
    }

    /**
     * Update an employee.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'can_logged_in' => filter_var($request->input('can_logged_in'), FILTER_VALIDATE_BOOLEAN),
                'mode' => $request->input('mode', 'edit'),
            ]);

            $employeeData = $request->only([
                'job_title',
                'floor',
                'office',
            ]);

            $employeeData['structure_uuid'] = $request->input('structure');
            $employeeData['can_logged_in'] = $request->can_logged_in;

            if ($employee->user) {
                $userData = [
                    'name' => $request->input('name'),
                    'role_uuid' => $request->input('role'),
                    'email' => $request->input('email') ?: null,
                    'phone' => $request->input('phone') ?: null,
                    'lang' => $request->input('lang'),
                    'status' => $request->input('status'),
                ];

                if ($request->can_logged_in) {
                    if ($request->filled('password')) {
                        $userData['password'] = Hash::make($request->input('password'));
                    }
                } else {
                    $userData['password'] = null;
                }

                $employee->user->fill($userData);
                $employee->user->save();
            }

            $employee->fill($employeeData);
            $employee->save();
            $employee->load(['user', 'user.role']);

            DB::commit();
            return new EmployeeResource($employee);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete employees.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $employees = Employee::with('user')->whereIn('id', $ids)->get();

            if ($employees->isEmpty()) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            foreach ($employees as $employee) {
                if ($employee->user) {
                    $employee->user->delete();
                }
            }

            Employee::whereIn('id', $ids)->delete();

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
