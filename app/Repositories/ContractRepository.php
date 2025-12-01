<?php

namespace App\Repositories;

use App\Helpers\FileHelper;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Supplier;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContractRepository
{
    /**
     * List contracts with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['contract_number', 'title', 'amount'];
        $sortable = ['contract_number', 'title', 'start_date', 'end_date', 'amount', 'signed_at', 'status'];
        $supplierId = $request->input('supplierId');

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Contract::join('suppliers', 'contracts.supplier_uuid', 'suppliers.uuid')
            ->select(
                'contracts.id',
                'contracts.uuid',
                'contracts.contract_number',
                'contracts.title',
                'contracts.start_date',
                'contracts.end_date',
                'contracts.amount',
                'contracts.signed_at',
                'contracts.status',
            )
            ->where('suppliers.id', $supplierId);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {

                    $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                }
            });
        }


        $query->orderBy("contracts.$sortBy", $sortOrder);


        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }




    /**
     * Store a new contract.
     */
    public function store(ContractRequest $request, Supplier $supplier)
    {
        $identifier = null;
        DB::beginTransaction();
        try {
            $request->merge([
                "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'mode' => $request->input('mode', 'view'),
                "created_by" => Auth::user()?->uuid,
                "updated_by" => Auth::user()?->uuid,
            ]);

            $contract = $supplier->contracts()->create($request->only([
                "contract_number",
                "title",
                "start_date",
                "end_date",
                "amount",
                "signed_at",
                "description",
                "status",
                "created_by",
                "updated_by",
            ]));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $contract->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            DB::commit();

            return new ContractResource($contract->load(['attachment']));
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($identifier)) {
                FileHelper::delete("uploads/{$identifier}");
            }
            throw $e;
        }
    }

    /**
     * Show a specific contract.
     */
    public function show(Contract $contract)
    {
        return ['contract' => new ContractResource($contract->load(['attachment']))];
    }

    /**
     * Update an contract.
     */
    public function update(ContractRequest $request, Contract $contract)
    {
        $oldFile = $contract->attachment?->identifier;
        $newFile = null;


        DB::beginTransaction();
        try {
            $request->merge([
                "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                "updated_by" => Auth::user()?->uuid,
            ]);

            $contract->fill($request->only([
                "contract_number",
                "title",
                "start_date",
                "end_date",
                "amount",
                "signed_at",
                "description",
                'status',
                'updated_by',
            ]))->save();


            if ($request->boolean('delete_file') && $oldFile) {
                FileHelper::delete("uploads/{$oldFile}");
                $contract->attachment()->delete();
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $newFile = FileHelper::upload($file, 'uploads');

                if ($oldFile && $oldFile !== $newFile) {
                    FileHelper::delete("uploads/{$oldFile}");
                    $contract->attachment()->delete();
                }

                $contract->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $newFile,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }


            DB::commit();

            $contract->load(['attachment']);

            return (new ContractResource($contract))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            if (!empty($identifier)) {
                FileHelper::delete("uploads/{$identifier}");
            }
            throw $e;
        }
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
                $deleted = Contract::whereIn('id', $ids)->delete();
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
