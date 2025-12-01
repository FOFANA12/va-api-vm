<?php

namespace App\Repositories\Settings;

use App\Http\Requests\Settings\FundingSourceRequest;
use App\Http\Resources\Settings\FundingSourceResource;
use App\Models\FundingSource;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FundingSourceRepository
{
    /**
     * List funding sources with pagination, search, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['funding_sources.name', 'funding_sources.description', 'structure'];
        $sortable = ['name', 'status', 'structure'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'funding_sources.id';

        $query = FundingSource::select(
            'funding_sources.id',
            'funding_sources.uuid',
            'funding_sources.name',
            'funding_sources.description',
            'funding_sources.status',
            'structures.name as structure'
        )
            ->leftJoin('structures', 'funding_sources.structure_uuid', '=', 'structures.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data.
     */
    public function requirements()
    {
        $structures = Structure::select('uuid', 'name', 'abbreviation')
            ->where('status', true)
            ->orderBy('id', 'desc')
            ->get();

        return ['structures' => $structures];
    }

    /**
     * Create a new funding source.
     */
    public function store(FundingSourceRequest $request)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $fundingSource = FundingSource::create($request->only([
            'name',
            'description',
            'structure_uuid',
            'status',
            'created_by',
            'updated_by'
        ]));

        return new FundingSourceResource($fundingSource);
    }

    /**
     * Show a specific funding source.
     */
    public function show(FundingSource $fundingSource)
    {
        return ['funding_source' => new FundingSourceResource($fundingSource->loadMissing('structure'))];
    }

    /**
     * Update an existing funding source.
     */
    public function update(FundingSourceRequest $request, FundingSource $fundingSource)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'edit'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $fundingSource->fill($request->only([
            'name',
            'description',
            'structure_uuid',
            'status',
            'updated_by'
        ]))->save();

        return new FundingSourceResource($fundingSource);
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

        DB::beginTransaction();
        try {
            $deleted = FundingSource::whereIn('id', $ids)->delete();
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
