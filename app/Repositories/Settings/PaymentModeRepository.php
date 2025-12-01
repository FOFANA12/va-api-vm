<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\PaymentModeRequest;
use App\Http\Resources\Settings\PaymentModeResource;

class PaymentModeRepository
{
    /**
     * List payment mode with pagination, filters, and sorting.
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

        $query = PaymentMode::select(
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

        $query->orderBy("payment_modes.$sortBy", $sortOrder);

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Create a new payment mode.
     */
    public function store(PaymentModeRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $payment_mode = PaymentMode::create($request->only([
            "name",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new PaymentModeResource($payment_mode);
    }

    /**
     * Show a specific payment mode.
     */
    public function show(PaymentMode $payment_mode)
    {
        return ['payment_mode' => new PaymentModeResource($payment_mode)];
    }

    /**
     * Update a payment mode.
     */
    public function update(PaymentModeRequest $request, PaymentMode $payment_mode)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $payment_mode->fill($request->only([
            'name',
            'status',
            'updated_by',
        ]))->save();

        return new PaymentModeResource($payment_mode);
    }

    /**
     * Delete multiple payment modes.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = PaymentMode::whereIn('id', $ids)->delete();
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
