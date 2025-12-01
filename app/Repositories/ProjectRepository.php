<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Beneficiary;
use App\Support\Currency;
use App\Models\FundingSource;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectState;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectRepository
{
    /**
     * List porjects with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['projects.name', 'program', 'projects.reference', 'responsible'];
        $sortable = ['name', 'program', 'reference', 'status', 'state', 'responsible', 'budget', 'start_date', 'end_date'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Project::select(
            'projects.id',
            'projects.uuid',
            'projects.reference',
            'projects.name',
            'projects.start_date',
            'projects.end_date',
            'projects.budget',
            'projects.status',
            'projects.state',
            'projects.responsible_uuid',
            'projects.currency',
            'responsibles.name as responsible',
            'programs.name as program'
        )
            ->leftJoin('users as responsibles', 'projects.responsible_uuid', '=', 'responsibles.uuid')
            ->join('programs', 'projects.program_uuid', '=', 'programs.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'responsible') {
                        $q->orWhere('responsibles.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else  if ($column === 'program') {
                        $q->orWhere('programs.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'responsible') {
            $query->orderBy('responsibles.name', $sortOrder);
        } else if ($sortBy === 'program') {
            $query->orderBy('programs.name', $sortOrder);
        } else {
            $query->orderBy("projects.$sortBy", $sortOrder);
        }

        return $perPage && (int) $perPage > 0
            ? $query->paginate((int) $perPage)
            : $query->get();
    }

    /**
     * Load requirements data
     */
    public function requirements()
    {
        $currency = Currency::getDefault();

        $responsibles = User::whereHas('employee')->select('uuid', 'name')
            ->where('status', true)->orderBy('id', 'desc')
            ->get();

        $programs = Program::select('uuid', 'name')
            ->whereNotIn('status', ['closed', 'stopped'])
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
            'programs' => $programs,
            'beneficiaries' => $beneficiaries,
            'funding_sources' => $fundingSources,
        ];
    }

    /**
     * Create a new project.
     */
    public function store(ProjectRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'program_uuid' => $request->input('program'),
                'responsible_uuid' => $request->input('responsible'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $project = Project::create($request->only([
                'name',
                'program_uuid',
                'start_date',
                'end_date',
                'currency',
                'responsible_uuid',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'created_by',
                'updated_by'
            ]));

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $project->beneficiaries()->sync($validBeneficiaries);

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
                    $project->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            $project->refresh();

            //Save initial status
            $status = ProjectStatus::create([
                'project_uuid' => $project->uuid,
                'project_id' => $project->id,
                'status_code' => $project->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            //Save initial state
            $state = ProjectState::create([
                'project_uuid' => $project->uuid,
                'project_id' => $project->id,
                'state_code' => $project->state,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $project->update([
                'reference' => ReferenceGenerator::generateProjectReference($project->id),
                'budget' => $totalBudget,
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            $project->loadMissing(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ProjectResource($project))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific project.
     */
    public function show(Project $project)
    {
        return ['project' => new ProjectResource($project->loadMissing(['responsible', 'beneficiaries', 'fundingSources']))];
    }

    /**
     * Update a project.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'program_uuid' => $request->input('program'),
                'responsible_uuid' => $request->input('responsible'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $project->fill($request->only([
                'name',
                'start_date',
                'end_date',
                'currency',
                'program_uuid',
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
            $project->beneficiaries()->sync($validBeneficiaries);

            $project->fundingSources()->detach();

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
                    $project->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $plannedBudget,
                    ]);
                    $totalBudget += $plannedBudget;
                }
            }

            DB::commit();

            $project->update(['budget' => $totalBudget]);
            $project->load(['responsible', 'beneficiaries', 'fundingSources']);

            return (new ProjectResource($project))->additional([
                'mode' => $request->input('mode', 'edit')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple project.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Project::whereIn('id', $ids)->delete();
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
