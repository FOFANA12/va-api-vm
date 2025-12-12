<?php

namespace App\Repositories;

use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\ContractType;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupplierRepository
{

    /**
     * List suppliers with pagination, filters, sorting.
     */
    public function index(Request $request)
    {

        $searchable = ['company_name', 'email', 'phone', 'name', 'tax_number', 'note'];
        $sortable = ['company_name', 'email', 'phone', 'name', 'tax_number', 'note', 'status'];

        $searchTerm = $request->input('searchTerm');
        $sortBy = in_array($request->input('sortBy'), $sortable) ? $request->input('sortBy') : 'id';
        $sortOrder = in_array(strtolower($request->input('sortOrder')), ['asc', 'desc']) ? strtolower($request->input('sortOrder')) : 'desc';
        $perPage = $request->input('perPage');

        $query = DB::table('suppliers')
            ->select(
                'suppliers.id',
                'suppliers.uuid',
                'suppliers.company_name',
                'suppliers.email',
                'suppliers.phone',
                'suppliers.note',
                'suppliers.tax_number',
                'suppliers.name',
                'suppliers.status'
            );

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {

                    $q->orWhere("suppliers.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }


        $query->orderBy($sortBy, $sortOrder);


        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Store a new supplier.
     */
    public function store(SupplierRequest $request)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
            'mode' => $request->input('mode', 'view'),
        ]);

        $supplier = Supplier::create($request->only([
            'company_name',
            'tax_number',
            'register_number',
            'establishment_year',
            'capital',
            'annual_turnover',
            'employees_count',
            'name',
            'phone',
            'whatsapp',
            'email',
            'address',
            'status',
            'created_by',
            'updated_by'
        ]));

        return new SupplierResource($supplier);
    }

    /**
     * Show a specific supplier.
     */
    public function show(Supplier $supplier)
    {
        return [
            'supplier' => new SupplierResource($supplier)
        ];
    }

    /**
     * Update a supplier.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $request->merge([
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'updated_by' => Auth::user()?->uuid,
            'mode' => $request->input('mode', 'edit'),
        ]);



        $supplier->fill($request->only([
            'company_name',
            'tax_number',
            'register_number',
            'establishment_year',
            'capital',
            'annual_turnover',
            'employees_count',
            'name',
            'phone',
            'whatsapp',
            'email',
            'address',
            'status',
            'updated_by'
        ]))->save();


        return (new SupplierResource($supplier))->additional([
            'mode' => $request->input('mode', 'edit')
        ]);
    }

    /**
     * Delete one or more suppliers.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Supplier::whereIn('id', $ids)->delete();
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
