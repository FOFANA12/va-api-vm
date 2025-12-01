<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\ActivityState;
use App\Models\ActivityStatus;
use App\Models\Beneficiary;
use App\Support\Currency;
use App\Models\FundingSource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ActivityRepository
{
    /**
     * List activities with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['activities.name', 'project', 'activities.reference', 'responsible'];
        $sortable = ['name', 'project', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Activity::select(
            'activities.id',
            'activities.uuid',
            'activities.reference',
            'activities.name',
            'activities.start_date',
            'activities.end_date',
            'activities.budget',
            'activities.status',
            'activities.state',
            'activities.responsible_uuid',
            'activities.currency',
            'responsibles.name as responsible',
            'projects.name as project'
        )
            ->leftJoin('users as responsibles', 'activities.responsible_uuid', '=', 'responsibles.uuid')
            ->join('projects', 'activities.project_uuid', '=', 'projects.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else  if ($column === 'project') {
                        $q->orWhere('projects.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else if ($sortBy === 'project') {
            $query->orderBy('projects.name', $sortOrder);
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
        $currency = Currency::getDefault();

        $responsibles = User::whereHas('employee')->select('uuid', 'name')
            ->where('status', true)->orderBy('id', 'desc')
            ->get();

        $projects = Project::select('uuid', 'name')
            ->where('status', '!=', 'done')
            ->orderBy('id', 'desc')
            ->get();

        $beneficiaries = Beneficiary::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $fundingSources = FundingSource::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();


        return [
            'currency' => $currency,
            'responsibles' => $responsibles,
            'projects' => $projects,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }
    /**
     * Store a new activity.
     */
    public function store(ActivityRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->merge([
                'project_uuid' => $request->input('project'),
                'responsible_uuid' => $request->input('responsible'),
                'created_uuid' => Auth::user()?->uuid,
                'updated_uuid' => Auth::user()?->uuid,
            ]);

            $activity = Activity::create($request->only([
                'name',
                'project_uuid',
                'start_date',
                'end_date',
                'currency',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'created_by',
                'updated_by',
            ]));

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $activity->beneficiaries()->sync($validBeneficiaries);

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            $totalBudget = 0;
            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $plannedBudget = $source['planned_amount'] ?? 0;
                    $activity->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $activity->refresh();

            //Save initial status
            $status = ActivityStatus::create([
                'activity_uuid' => $activity->uuid,
                'activity_id' => $activity->id,
                'status_code' => $activity->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = ActivityState::create([
                'activity_uuid' => $activity->uuid,
                'activity_id' => $activity->id,
                'state_code' => $activity->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $activity->update([
                'reference' => ReferenceGenerator::generateActivityReference($activity->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $activity->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ActivityResource($activity))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific activity.
     */
    public function show(Activity $activity)
    {
        return ['activity' => new ActivityResource($activity->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update an existing activity.
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'project_uuid' => $request->input('project'),
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $activity->fill($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'project_uuid',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'updated_by'
            ]))->save();

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $activity->beneficiaries()->sync($validBeneficiaries);

            $activity->fundingSources()->detach();

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            $totalBudget = 0;
            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $plannedBudget = $source['planned_amount'] ?? 0;
                    $activity->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $activity->update(['budget' => $totalBudget]);
            $activity->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ActivityResource($activity))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete one or multiple activities.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Activity::whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            DB::commit();
        } catch (RuntimeException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($e->getCode() === '23000') {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }
}
