<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\BeneficiaryRequest;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Settings\BeneficiaryResource;
use App\Models\Beneficiary;

class BeneficiaryRepository
{
    /**
     * List beneficiaries with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['name', 'email', 'phone', 'status'];
        $sortable = ['name', 'email', 'phone', 'status'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'beneficiaries.id';

        $query = Beneficiary::select(
            'beneficiaries.id',
            'beneficiaries.uuid',
            'beneficiaries.name',
            'beneficiaries.email',
            'beneficiaries.phone',
            'beneficiaries.status',
            'beneficiaries.created_by',
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
     * Store a new beneficiaries.
     */
    public function store(BeneficiaryRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'view'),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $beneficiary = Beneficiary::create($request->only([
            "name",
            "email",
            "phone",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new BeneficiaryResource($beneficiary);
    }

    public function show(Beneficiary $beneficiary)
    {
        return ['beneficiary' => new BeneficiaryResource($beneficiary)];
    }

    public function update(BeneficiaryRequest $request, Beneficiary $beneficiary)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'edit'),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $beneficiary->fill($request->only([
            'name',
            "email",
            "phone",
            'status',
            'updated_by',
        ]))->save();

        return new BeneficiaryResource($beneficiary);
    }

    /**
     * Delete multiple funding sources.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = Beneficiary::whereIn('id', $ids)->delete();
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
