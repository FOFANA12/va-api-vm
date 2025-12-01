<?php

namespace App\Repositories\Settings;

use RuntimeException;
use Illuminate\Http\Request;
use App\Models\IndicatorCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\IndicatorCategoryRequest;
use App\Http\Resources\Settings\IndicatorCategoryResource;

class IndicatorCategoryRepository
{
    /**
     * List indicator categories with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'status'];
        $sortable = ['name', 'status'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'indicator_categories.id';

        $query = IndicatorCategory::select(
            'indicator_categories.id',
            'indicator_categories.uuid',
            'indicator_categories.name',
            'indicator_categories.status',
        );

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
     * Store a new indicator category.
     */
    public function store(IndicatorCategoryRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $indicatorCategory = IndicatorCategory::create($request->only([
            "name",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new IndicatorCategoryResource($indicatorCategory);
    }

    /**
     * Show a specific indicator category.
     */
    public function show(IndicatorCategory $indicatorCategory)
    {
        return ['indicator_category' => new IndicatorCategoryResource($indicatorCategory)];
    }

    /**
     * Update an indicator category.
     */
    public function update(IndicatorCategoryRequest $request, IndicatorCategory $indicatorCategory)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $indicatorCategory->fill($request->only([
            'name',
            'status',
            'updated_by',
        ]))->save();

        return new IndicatorCategoryResource($indicatorCategory);
    }

    /**
     * Delete multiple indicator categories.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = IndicatorCategory::whereIn('id', $ids)->delete();
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
