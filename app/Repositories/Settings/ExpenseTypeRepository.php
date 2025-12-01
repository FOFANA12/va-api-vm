<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\ExpenseTypeRequest;
use App\Http\Resources\Settings\ExpenseTypeResource;

class ExpenseTypeRepository
{
    /**
     * List expense type with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name'];
        $sortable = ['name', 'status'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = ExpenseType::select(
            'id',
            'uuid',
            'name',
            'status',
            'created_by',
        );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {

                    $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }

        $query->orderBy("expense_types.$sortBy", $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Create a new expense type.
     */
    public function store(ExpenseTypeRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $expenseType = ExpenseType::create($request->only([
            "name",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new ExpenseTypeResource($expenseType);
    }

    /**
     * Show a specific expense type.
     */
    public function show(ExpenseType $expenseType)
    {
        return ['expense_type' => new ExpenseTypeResource($expenseType)];
    }

    /**
     * Update a expense type.
     */
    public function update(ExpenseTypeRequest $request, ExpenseType $expenseType)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $expenseType->fill($request->only([
            'name',
            'status',
            'updated_by',
        ]))->save();

        return new ExpenseTypeResource($expenseType);
    }

    /**
     * Delete multiple expense types.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = ExpenseType::whereIn('id', $ids)->delete();
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
