<?php

namespace App\Repositories;

use App\Http\Requests\StrategicStakeholderRequest;
use App\Http\Resources\StrategicStakeholderResource;
use App\Models\StrategicMap;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StrategicStakeholder;

class StrategicStakeholderRepository
{
    /**
     * List strategic stakeholders with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['organization', 'responsible', 'email', 'phone'];
        $sortable = ['organization', 'responsible', 'email', 'phone', 'created_at'];

        $strategicMapId = $request->input('strategicMapId');
        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'strategic_stakeholders.id';

        $query = StrategicStakeholder::join('strategic_maps', 'strategic_stakeholders.strategic_map_uuid', '=', 'strategic_maps.uuid')
            ->select(
                'strategic_stakeholders.id',
                'strategic_stakeholders.uuid',
                'strategic_stakeholders.organization',
                'strategic_stakeholders.responsible',
                'strategic_stakeholders.email',
                'strategic_stakeholders.phone',
            );

        if (!empty($strategicMapId)) {
            $query->where('strategic_maps.id', $strategicMapId);
        }

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
     * Store a new strategic stakeholder.
     */
    public function store(StrategicStakeholderRequest $request, StrategicMap $strategicMap)
    {
        $request->merge([
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $strategicStakeholder = $strategicMap->stakeholders()->create($request->only([
            "organization",
            "responsible",
            "email",
            "phone",
            "strategic_map_uuid",
            "created_by",
            "updated_by",
        ]));

        return new StrategicStakeholderResource($strategicStakeholder);
    }

    /**
     * Show a strategic stakeholder.
     */
    public function show(StrategicStakeholder $strategicStakeholder)
    {
        return ['strategic_stakeholder' => new StrategicStakeholderResource($strategicStakeholder)];
    }

    /**
     * Update a strategic stakeholder.
     */
    public function update(StrategicStakeholderRequest $request, StrategicStakeholder $strategicStakeholder)
    {
        $request->merge([
            "updated_by" => Auth::user()?->uuid,
        ]);

        $strategicStakeholder->fill($request->only([
            "organization",
            "responsible",
            "email",
            "phone",
            "updated_by",
        ]))->save();

        return new StrategicStakeholderResource($strategicStakeholder);
    }

    /**
     * Delete multiple strategic stakeholders.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        try {
            DB::transaction(function () use ($ids) {
                $deleted = StrategicStakeholder::whereIn('id', $ids)->delete();
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
