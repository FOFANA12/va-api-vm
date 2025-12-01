<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\CurrencyRequest;
use App\Http\Resources\Settings\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CurrencyRepository
{
    /**
     * List currencies with pagination, search, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'code'];
        $sortable = ['name', 'code', 'status', 'is_default'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Currency::query();

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
     * Create a new currency.
     */
    public function store(CurrencyRequest $request)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'is_default' => filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $currency = Currency::create($request->only([
            'name',
            'code',
            'status',
            'is_default',
            'created_by',
            'updated_by'
        ]));

        if ($currency->is_default) {
            Currency::where('id', '<>', $currency->id)->update(['is_default' => false]);
        }

        return new CurrencyResource($currency);
    }

    /**
     * Show a specific currency.
     */
    public function show(Currency $currency)
    {
        return ['currency' => new CurrencyResource($currency)];
    }

    /**
     * Get the default currency.
     */
    public function getDefaultCurrency()
    {
        $currency = Currency::where('is_default', true)->first();
        return ['currency' => $currency ? new CurrencyResource($currency) : null];
    }

    /**
     * Update an existing currency.
     */
    public function update(CurrencyRequest $request, Currency $currency)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'is_default' => filter_var($request->input('is_default'), FILTER_VALIDATE_BOOLEAN),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $currency->fill($request->only([
            'name',
            'code',
            'status',
            'is_default',
            'updated_by'
        ]))->save();

        if ($currency->is_default) {
            Currency::where('id', '<>', $currency->id)->update(['is_default' => false]);
        }

        return new CurrencyResource($currency);
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
            $deleted = Currency::whereIn('id', $ids)->delete();
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
