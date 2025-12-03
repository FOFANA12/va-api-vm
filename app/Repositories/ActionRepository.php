<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActionRequest;
use App\Http\Resources\ActionResource;
use App\Jobs\EvaluateActionJob;
use App\Jobs\UpdateActionTotalsJob;
use App\Models\Action;
use App\Models\ActionDomain;
use App\Models\ActionPlan;
use App\Models\ActionStatus as ModelsActionStatus;
use App\Models\Attachment;
use App\Models\Beneficiary;
use App\Models\CapabilityDomain;
use App\Support\Currency;
use App\Models\DelegatedProjectOwner;
use App\Models\Department;
use App\Models\FileType;
use App\Models\FundingSource;
use App\Models\Municipality;
use App\Models\ProjectOwner;
use App\Models\Region;
use App\Models\Stakeholder;
use App\Models\StrategicDomain;
use App\Models\Structure;
use App\Support\GenerateDocumentTypes;
use App\Support\PriorityLevel;
use App\Support\RiskLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActionRepository
{
    /**
     * List actions with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'name', 'project_owner', 'structure'];
        $sortable = ['reference', 'name', 'priority', 'project_owner', 'structure', 'risk_level', 'status', 'state', 'actual_progress_percent', 'start_date', 'end_date', 'total_budget'];


        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Action::join('structures', 'actions.structure_uuid', '=', 'structures.uuid')
            ->join('project_owners', 'actions.project_owner_uuid', '=', 'project_owners.uuid')
            ->select(
                'actions.id as id',
                'actions.uuid',
                'actions.reference',
                'actions.name',
                'actions.priority',
                'actions.risk_level',
                'structures.name as structure',
                'project_owners.name as projectOwner',
                'actions.actual_progress_percent',
                'actions.status',
                'actions.state',
                'start_date',
                'end_date',
                'total_budget',
                'total_receipt_fund',
                'total_disbursement_fund',
                'actions.is_planned',
            );


        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'project_owner') {
                        $q->orWhere('project_owners.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere("actions.$column", 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else if ($sortBy === 'project_owner') {
            $query->orderBy("project_owners.$sortBy", $sortOrder);
        } else {
            $query->orderBy("actions.$sortBy", $sortOrder);
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
        $structures = Structure::query()
            ->where('status', true)
            // ->whereIn('type', ['DIRECTION'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'type', 'parent_uuid')
            ->get();

        $directionUuids = $structures->pluck('uuid')->toArray();

        $actionPlans = ActionPlan::query()
            ->where('status', true)
            ->whereIn('structure_uuid', $directionUuids)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'structure_uuid')
            ->get();

        $projectOwners = ProjectOwner::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'structure_uuid')
            ->get()
            ->map(function ($owner) {
                return [
                    'uuid' => $owner->uuid,
                    'name' => $owner->name,
                    'structure_uuid' => $owner->structure_uuid,
                ];
            });

        $delegatedProjectOwners = DelegatedProjectOwner::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'project_owner_uuid')
            ->get();


        $regions = Region::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $departments = Department::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'region_uuid')
            ->get();

        $municipalities = Municipality::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'department_uuid')
            ->get();

        $actionDomains = ActionDomain::whereNotIn('status', ['closed', 'stopped'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $strategicDomains = StrategicDomain::whereNotIn('status', ['closed', 'stopped'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'action_domain_uuid')
            ->get();

        $capabilityDomains = CapabilityDomain::whereNotIn('status', ['closed', 'stopped'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'strategic_domain_uuid')
            ->get();


        $generateDocumentTypes =  collect(GenerateDocumentTypes::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $priorityLevels =  collect(PriorityLevel::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'color' => $item['color'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $riskLevels =  collect(RiskLevel::all())->map(function ($item) {
            return [
                'color' => $item['color'],
                'code' => $item['code'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        $beneficiaries = Beneficiary::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $stakeholders = Stakeholder::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name')
            ->get();

        $fundingSources = FundingSource::where('status', true)
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'structure_uuid')
            ->get()
            ->map(function ($source) {
                return [
                    'uuid' => $source->uuid,
                    'name' => $source->name,
                    'structure_uuid' => $source->structure_uuid,
                ];
            });

        $currency = Currency::getDefault();

        return [
            'structures' => $structures,
            'action_plans' => $actionPlans,
            'project_owners' => $projectOwners,
            'delegated_project_owners' => $delegatedProjectOwners,
            'regions' => $regions,
            'departments' => $departments,
            'municipalities' => $municipalities,
            'action_domains' => $actionDomains,
            'strategic_domains' => $strategicDomains,
            'capability_domains' => $capabilityDomains,
            'risk_levels' => $riskLevels,
            'priority_levels' => $priorityLevels,
            'generate_document_types' => $generateDocumentTypes,

            'beneficiaries' => $beneficiaries,
            'stakeholders' => $stakeholders,
            'funding_sources' => $fundingSources,
            'currency' => $currency,
        ];
    }

    /**
     * Store a new actions.
     */
    public function store(ActionRequest $request)
    {

        DB::beginTransaction();
        try {
            $request->merge([
                'structure_uuid' => $request->input('structure'),
                'action_plan_uuid' => $request->input('action_plan'),
                'project_owner_uuid' => $request->input('project_owner'),
                'delegated_project_owner_uuid' => $request->input('delegated_project_owner'),

                'region_uuid' => $request->input('region'),
                'department_uuid' => $request->input('department'),
                'municipality_uuid' => $request->input('municipality'),
                'action_domain_uuid' => $request->input('action_domain'),
                'strategic_domain_uuid' => $request->input('strategic_domain'),
                'capability_domain_uuid' => $request->input('capability_domain'),

                'mode' => $request->input('mode', 'view'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
                'status_changed_at' => now(),
                'status_changed_by' => Auth::user()?->uuid,
            ]);

            $action = Action::create($request->only([
                'name',
                'priority',
                'risk_level',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'generate_document_type',
                'structure_uuid',
                'action_plan_uuid',
                'project_owner_uuid',
                'delegated_project_owner_uuid',
                'currency',
                'region_uuid',
                'department_uuid',
                'municipality_uuid',
                'action_domain_uuid',
                'strategic_domain_uuid',
                'capability_domain_uuid',
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
            $action->beneficiaries()->sync($validBeneficiaries);

            $stakeholderUuids = collect($request->stakeholders)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validStakeholders = Stakeholder::whereIn('uuid', $stakeholderUuids)
                ->pluck('uuid')
                ->toArray();
            $action->stakeholders()->sync($validStakeholders);

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $action->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $source['planned_amount'] ?? 0,
                    ]);
                }
            }

            $action->refresh();

            $fileTypes = FileType::where('status', true)->whereNotNull('identifier')->get();

            foreach ($fileTypes as $fileType) {
                $sourcePath = "uploads/{$fileType->identifier}";

                if (Storage::exists($sourcePath)) {
                    $extension = pathinfo($fileType->identifier, PATHINFO_EXTENSION);
                    $newIdentifier = Str::uuid() . '.' . $extension;
                    $destinationPath = "uploads/{$newIdentifier}";
                    Storage::copy($sourcePath, $destinationPath);

                    Attachment::create([
                        'title' => $fileType->name,
                        'original_name' => $fileType->original_name,
                        'mime_type' => $fileType->mime_type,
                        'identifier' => $newIdentifier,
                        'size' => $fileType->size,
                        'attachable_id' => $action->id,
                        'attachable_type' => Action::tableName(),
                        'file_type_uuid' => $fileType->uuid,
                        'comment' => $fileType->comment ?? null,
                        'uploaded_by' => Auth::user()?->uuid,
                        'uploaded_at' => now(),
                    ]);
                }
            }

            //Save initial status
            $status = ModelsActionStatus::create([
                'action_uuid' => $action->uuid,
                'action_id' => $action->id,
                'status_code' => $action->status,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $abbStructure =  Structure::where('uuid', $request->input('structure'))->value('abbreviation');
            $action->update([
                'reference' => ReferenceGenerator::generateActionReference($action->id, $abbStructure),
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            dispatch(new UpdateActionTotalsJob($action->uuid));
            dispatch(new EvaluateActionJob($action->uuid));

            $action->load([
                'structure',
                'actionPlan',
                'projectOwner',
                'delegatedProjectOwner',
                'region',
                'department',
                'municipality',
                'actionDomain',
                'strategicDomain',
                'capabilityDomain',
                'beneficiaries',
                'stakeholders',
                'fundingSources',
                'statusChangedBy',
            ]);

            DB::commit();

            return new ActionResource($action);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific action.
     */
    public function show(Action $action)
    {
        return ['action' => new ActionResource($action->load([
            'structure',
            'actionPlan',
            'projectOwner',
            'delegatedProjectOwner',
            'region',
            'department',
            'municipality',
            'actionDomain',
            'strategicDomain',
            'capabilityDomain',
            'beneficiaries',
            'stakeholders',
            'fundingSources',
            'statusChangedBy',
        ]))];
    }

    /**
     * Update an action.
     */
    public function update(ActionRequest $request, Action $action)
    {
        DB::beginTransaction();
        try {
            $request->merge([
                'structure_uuid' => $request->input('structure'),
                'action_plan_uuid' => $request->input('action_plan'),
                'project_owner_uuid' => $request->input('project_owner'),
                'delegated_project_owner_uuid' => $request->input('delegated_project_owner'),

                'region_uuid' => $request->input('region'),
                'department_uuid' => $request->input('department'),
                'municipality_uuid' => $request->input('municipality'),
                'action_domain_uuid' => $request->input('action_domain'),
                'strategic_domain_uuid' => $request->input('strategic_domain'),
                'capability_domain_uuid' => $request->input('capability_domain'),

                'mode' => $request->input('mode', 'edit'),
                'updated_by' => Auth::user()?->uuid,
            ]);

            $action->fill($request->only([
                'name',
                'priority',
                'risk_level',
                'description',
                'prerequisites',
                'impacts',
                'risks',
                'generate_document_type',
                'structure_uuid',
                'action_plan_uuid',
                'project_owner_uuid',
                'delegated_project_owner_uuid',
                'currency',
                'region_uuid',
                'department_uuid',
                'municipality_uuid',
                'action_domain_uuid',
                'strategic_domain_uuid',
                'capability_domain_uuid',
                'updated_by'
            ]));

            $action->save();

            $beneficiaryUuids = collect($request->beneficiaries)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validBeneficiaries = Beneficiary::whereIn('uuid', $beneficiaryUuids)
                ->pluck('uuid')
                ->toArray();
            $action->beneficiaries()->sync($validBeneficiaries);

            $stakeholderUuids = collect($request->stakeholders)
                ->pluck('uuid')
                ->filter()
                ->toArray();
            $validStakeholders = Stakeholder::whereIn('uuid', $stakeholderUuids)
                ->pluck('uuid')
                ->toArray();
            $action->stakeholders()->sync($validStakeholders);

            $action->fundingSources()->detach();

            $requestedUuids = collect($request->funding_sources)
                ->pluck('uuid')
                ->filter()
                ->toArray();

            $validFundingSources = FundingSource::whereIn('uuid', $requestedUuids)
                ->pluck('uuid')
                ->toArray();

            foreach ($request->funding_sources as $source) {
                if (in_array($source['uuid'], $validFundingSources)) {
                    $action->fundingSources()->attach($source['uuid'], [
                        'planned_budget' => $source['planned_amount'] ?? 0,
                    ]);
                }
            }

            dispatch(new UpdateActionTotalsJob($action->uuid));

            $action->load([
                'structure',
                'actionPlan',
                'projectOwner',
                'delegatedProjectOwner',
                'region',
                'department',
                'municipality',
                'actionDomain',
                'strategicDomain',
                'capabilityDomain',
                'beneficiaries',
                'stakeholders',
                'fundingSources',
                'statusChangedBy',
            ]);
            DB::commit();

            return new ActionResource($action);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete action(s).
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = Action::whereIn('id', $ids)->delete();
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
