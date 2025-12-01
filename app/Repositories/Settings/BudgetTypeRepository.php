<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\BudgetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\BudgetTypeRequest;
use App\Http\Resources\Settings\BudgetTypeResource;

class BudgetTypeRepository
{
    /**
     * List budget type with pagination, filters, and sorting.
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

        $query = BudgetType::select(
            'id',
            'uuid',
            'name',
            'status',
        );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {

                    $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }

        $query->orderBy("budget_types.$sortBy", $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Create a new budget type.
     */
    public function store(BudgetTypeRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $payment_mode = BudgetType::create($request->only([
            "name",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new BudgetTypeResource($payment_mode);
    }

    /**
     * Show a specific budget type.
     */
    public function show(BudgetType $budget_type)
    {
        return ['budget_type' => new BudgetTypeResource($budget_type)];
    }

    /**
     * Update a budget type.
     */
    public function update(BudgetTypeRequest $request, BudgetType $budget_type)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $budget_type->fill($request->only([
            'name',
            'status',
            'updated_by',
        ]))->save();

        return new BudgetTypeResource($budget_type);
    }

    /**
     * Delete multiple budget type.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {

                $deleted = BudgetType::whereIn('id', $ids)->delete();
                if ($deleted === 0) {
                    throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
                }
            });
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
