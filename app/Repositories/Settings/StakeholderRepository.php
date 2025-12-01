<?php

namespace App\Repositories\Settings;

use RuntimeException;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Settings\StakeholderRequest;
use App\Http\Resources\Settings\StakeholderResource;

class StakeholderRepository
{
    /**
     * List stakeholders with pagination, filters, and sorting.
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
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'stakeholders.id';

        $query = Stakeholder::select(
            'stakeholders.id',
            'stakeholders.uuid',
            'stakeholders.name',
            'stakeholders.email',
            'stakeholders.phone',
            'stakeholders.status',
            'stakeholders.created_by',
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
     * Store a new stakeholder.
     */
    public function store(StakeholderRequest $request)
    {

        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'view'),
            "created_by" => Auth::user()?->uuid,
            "updated_by" => Auth::user()?->uuid,
        ]);

        $stakeholder = Stakeholder::create($request->only([
            "name",
            "email",
            "phone",
            "status",
            "created_by",
            "updated_by",
        ]));

        return new StakeholderResource($stakeholder);
    }

    public function show(Stakeholder $stakeholder)
    {
        return ['stakeholder' => new StakeholderResource($stakeholder)];
    }

    public function update(StakeholderRequest $request, Stakeholder $stakeholder)
    {
        $request->merge([
            "status" => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            "mode" => $request->input('mode', 'edit'),
            "updated_by" => Auth::user()?->uuid,
        ]);

        $stakeholder->fill($request->only([
            'name',
            "email",
            "phone",
            'status',
            'updated_by',
        ]))->save();

        return new StakeholderResource($stakeholder);
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
                $deleted = Stakeholder::whereIn('id', $ids)->delete();
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
