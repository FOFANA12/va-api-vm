<?php

namespace App\Repositories;

use App\Helpers\ReferenceGenerator;
use App\Http\Requests\ActionPlanRequest;
use App\Http\Resources\ActionPlanResource;
use App\Jobs\EvaluateActionJob;
use App\Jobs\UpdateActionTotalsJob;
use App\Models\Action;
use App\Models\ActionPlan;
use App\Models\ActionStatus;
use App\Models\Attachment;
use App\Models\FileType;
use App\Models\Structure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ActionPlanRepository
{
    /**
     * List action plans with pagination, filters, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['action_plans.name', 'action_plans.reference', 'structure', 'responsible'];
        $sortable = ['name', 'reference', 'status', 'start_date', 'end_date', 'structure', 'responsible'];

        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'action_plans.id';

        $query = ActionPlan::select(
            'action_plans.id',
            'action_plans.uuid',
            'action_plans.name',
            'action_plans.reference',
            'structures.name as structure',
            'users.name as responsible',
            'action_plans.status',
            'action_plans.start_date',
            'action_plans.end_date',
        )
            ->leftJoin('structures', 'action_plans.structure_uuid', '=', 'structures.uuid')
            ->leftJoin('users', 'action_plans.responsible_uuid', '=', 'users.uuid');

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm, $searchable) {
                foreach ($searchable as $column) {
                    if ($column === 'structure') {
                        $q->orWhere('structures.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else if ($column === 'responsible') {
                        $q->orWhere('users.name', 'LIKE', '%' . strtolower($searchTerm) . '%');
                    } else {
                        $q->orWhere($column, 'LIKE', '%' . strtolower($searchTerm) . '%');
                    }
                }
            });
        }

        if ($sortBy === 'structure') {
            $query->orderBy('structures.name', $sortOrder);
        } else if ($sortBy === 'responsible') {
            $query->orderBy('users.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
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
            ->whereIn('type', ['OPERATIONAL'])
            ->orderBy('id', 'desc')
            ->select('uuid', 'name', 'type')
            ->get();

        $users = User::select('uuid', 'name', 'email')
            ->where('status', true)
            ->whereHas('employee')->with([
                'employee:id,user_uuid,structure_uuid',
            ])
            ->orderBy('id', 'desc')
            ->get()->map(function ($user) {
                return [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'structure_uuid' => $user->employee?->structure_uuid,
                ];
            });

        return ['structures' => $structures, 'users' => $users];
    }

    /**
     * Create a new action plan.
     */
    public function store(ActionPlanRequest $request)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'responsible_uuid' => $request->input('responsible'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'view'),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionPlan = ActionPlan::create($request->only([
            'structure_uuid',
            'responsible_uuid',
            'name',
            'description',
            'start_date',
            'end_date',
            'status',
            'created_by',
            'updated_by',
        ]));

        $actionPlan->update([
            'reference' => ReferenceGenerator::generateActionPlanReference($actionPlan->id),
        ]);

        return new ActionPlanResource($actionPlan);
    }

    /**
     * Copy a new action plan with all its actions.
     */
    public function duplicate(ActionPlanRequest $request)
    {
        DB::beginTransaction();

        try {
            $originalPlanId = $request->input('original_action_plan_id');
            $originalPlan = ActionPlan::with('structure')->findOrFail($originalPlanId);

            $request->merge([
                'structure_uuid' => $request->input('structure'),
                'responsible_uuid' => $request->input('responsible'),
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'mode' => $request->input('mode', 'view'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $newPlan = ActionPlan::create($request->only([
                'structure_uuid',
                'responsible_uuid',
                'name',
                'description',
                'start_date',
                'end_date',
                'status',
                'created_by',
                'updated_by',
            ]));

            $newPlan->update([
                'reference' => ReferenceGenerator::generateActionPlanReference($newPlan->id),
            ]);

            $originalActions = Action::with([
                'beneficiaries',
                'stakeholders',
                'fundingSources',
            ])->where('action_plan_uuid', $originalPlan->uuid)->get();

            foreach ($originalActions as $oldAction) {
                // Duplication de l'action
                $newAction = $oldAction->replicate([
                    'uuid',
                    'reference',
                    'state',
                    'status',
                    'status_changed_at',
                    'status_changed_by',
                    'created_at',
                    'updated_at',
                    'start_date',
                    'end_date',
                    'actual_start_date',
                    'actual_end_date',
                    'total_budget',
                    'total_receipt_fund',
                    'total_disbursement_fund',
                    'frequency_unit',
                    'frequency_value',
                    'is_planned',
                    'actual_progress_percent',
                    'failed',
                    'alert',
                ]);

                $newAction->reference = null;
                $newAction->action_plan_uuid = $newPlan->uuid;
                $newAction->created_by = Auth::user()?->uuid;
                $newAction->updated_by = Auth::user()?->uuid;
                $newAction->save();

                // Copie des relations
                $newAction->beneficiaries()->sync($oldAction->beneficiaries->pluck('uuid'));
                $newAction->stakeholders()->sync($oldAction->stakeholders->pluck('uuid'));

                foreach ($oldAction->fundingSources as $funding) {
                    $newAction->fundingSources()->attach($funding->uuid, [
                        'planned_budget' => $funding->pivot->planned_budget ?? 0,
                    ]);
                }

                $newAction->refresh();

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
                            'attachable_id' => $newAction->id,
                            'attachable_type' => Action::tableName(),
                            'file_type_uuid' => $fileType->uuid,
                            'comment' => $fileType->comment ?? null,
                            'uploaded_by' => Auth::user()?->uuid,
                            'uploaded_at' => now(),
                        ]);
                    }
                }

                //Save initial status
                $status = ActionStatus::create([
                    'action_uuid' => $newAction->uuid,
                    'action_id' => $newAction->id,
                    'status_code' => $newAction->status,
                    'status_date' => now(),
                    'created_by' => Auth::user()?->uuid,
                    'updated_by' => Auth::user()?->uuid,
                ]);

                $abbStructure =  Structure::where('uuid', $request->input('structure'))->value('abbreviation');
                $newAction->update([
                    'reference' => ReferenceGenerator::generateActionReference($newAction->id, $abbStructure),
                    'status' => $status->status_code,
                    'status_changed_at' => $status->status_date,
                    'status_changed_by' => $status->created_by,
                ]);

                dispatch(new UpdateActionTotalsJob($newAction->uuid));
                dispatch(new EvaluateActionJob($newAction->uuid));
            }

            DB::commit();

            return new ActionPlanResource($newPlan->load(['structure', 'responsible']));
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Show a specific action plan.
     */
    public function show(ActionPlan $actionPlan)
    {
        return ['action_plan' => new ActionPlanResource($actionPlan->loadMissing(['structure', 'responsible']))];
    }

    /**
     * Update an action plan.
     */
    public function update(ActionPlanRequest $request, ActionPlan $actionPlan)
    {
        $request->merge([
            'structure_uuid' => $request->input('structure'),
            'responsible_uuid' => $request->input('responsible'),
            'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            'mode' => $request->input('mode', 'edit'),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $actionPlan->fill($request->only([
            'structure_uuid',
            'responsible_uuid',
            'name',
            'description',
            'start_date',
            'end_date',
            'status',
            'updated_by',
        ]));

        if (empty($actionPlan->reference)) {
            $actionPlan->reference = ReferenceGenerator::generateActionPlanReference($actionPlan->id);
        }

        $actionPlan->save();

        return new ActionPlanResource($actionPlan);
    }

    /**
     * Delete multiple action plans.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = ActionPlan::whereIn('id', $ids)->delete();

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
