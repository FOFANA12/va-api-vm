<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\ProcurementModeRequest;
use App\Http\Resources\Settings\ProcurementModeResource;
use App\Models\ContractType;
use App\Models\ProcurementMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProcurementModeRepository
{
    /**
     * List procurement modes with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['procurement_modes.name', 'contract_type'];
        $sortable = ['procurement_modes.name', 'procurement_modes.status', 'contract_type'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'procurement_modes.id';

        $query = ProcurementMode::select(
            'procurement_modes.id',
            'procurement_modes.uuid',
            'procurement_modes.contract_type_uuid',
            'procurement_modes.name',
            'procurement_modes.status',
            'contract_types.name as contract_type'
        )
            ->leftJoin('contract_types', 'procurement_modes.contract_type_uuid', '=', 'contract_types.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'contract_type') {
                        $q->orWhere('contract_types.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } elseif($column === 'name'){
                        $q->onWhere('procurement_modes.name', 'LIKE', '%' . strtolower($searchTerm). '%');
                    }
                     else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'contract_type') {
            $query->orderBy('contract_types.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data for forms.
     */
    public function requirements()
    {
        $contractTypes = ContractType::select('uuid', 'name')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return [
            'contract_types' => $contractTypes
        ];
    }

    /**
     * Store a newly created procurement mode.
     */
    public function store(ProcurementModeRequest $request)
    {
        $request->merge([
            'contract_type_uuid' => $request->input('contract_type'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $procurementMode = ProcurementMode::create($request->only([
            'contract_type_uuid',
            'name',
            'duration',
            'status',
            'created_by',
            'updated_by'
        ]));

        return new ProcurementModeResource($procurementMode);
    }

    /**
     * Show details of a procurement mode.
     */
    public function show(ProcurementMode $procurementMode)
    {
        return ['procurement_mode' => new ProcurementModeResource(
            $procurementMode->loadMissing('contractType')
        )];
    }

    /**
     * Update an existing procurement mode.
     */
    public function update(ProcurementModeRequest $request, ProcurementMode $procurementMode)
    {
        $request->merge([
            'contract_type_uuid' => $request->input('contract_type'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $procurementMode->fill($request->only([
            'contract_type_uuid',
            'name',
            'duration',
            'status',
            'updated_by'
        ]))->save();

        return new ProcurementModeResource($procurementMode);
    }

    /**
     * Delete one or multiple procurement modes.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = ProcurementMode::whereIn('id', $ids)->delete();
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
