<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Http\Requests\ObjectiveAlignmentRequest;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\Action;
use App\Models\ActionObjectiveAlignment;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Support\ActionState;
use App\Support\ActionStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class StrategicObjectiveAlignmentRepository
{
    /**
     * List strategic actions with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $searchable = ['reference', 'name', 'project_owner', 'structure'];
        $sortable = ['reference', 'name', 'priority', 'project_owner', 'structure', 'risk_level', 'status'];

        $objectiveId   = $request->input('objectiveId');
        $searchTerm = $request->input('searchTerm');
        $sortByInput = $request->input('sortBy');
        $sortOrderInput = strtolower($request->input('sortOrder', 'desc'));
        $perPage = $request->input('perPage');

        $sortOrder = in_array($sortOrderInput, ['asc', 'desc']) ? $sortOrderInput : 'desc';
        $sortBy = in_array($sortByInput, $sortable) ? $sortByInput : 'id';

        $query = Action::join('action_objective_alignments', 'actions.uuid', '=', 'action_objective_alignments.action_uuid')
            ->join('strategic_objectives', 'action_objective_alignments.objective_uuid', '=', 'strategic_objectives.uuid')
            ->join('structures', 'actions.structure_uuid', '=', 'structures.uuid')
            ->join('project_owners', 'actions.project_owner_uuid', '=', 'project_owners.uuid')
            ->select(
                'action_objective_alignments.id as id',
                'actions.id as action_id',
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
                'actions.start_date',
                'actions.end_date',
                'actions.total_budget',
                'actions.total_receipt_fund',
                'actions.total_disbursement_fund',
                'actions.is_planned',

            )
            ->where('strategic_objectives.id', $objectiveId);

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
     * Retrieve all active structures.
     */
    public function getStructures()
    {
        $structures = Structure::where('status', true)
            ->whereHas('actionPlans', function ($q) {
                $q->where('status', true);
            })
            ->orderBy('name')
            ->select('id', 'uuid', 'name', 'abbreviation', 'type')
            ->get();

        return [
            "structures" => $structures
        ];
    }


    /**
     * Load actions for a strategic objective within a structure
     */
    public function getActions(Structure $structure, StrategicObjective $strategicObjective)
    {
        $alreadyAlignedActionUuids = $strategicObjective->actions()
            ->pluck('actions.uuid')
            ->toArray();

        $structure->load([
            'actionPlans' => function ($q) {
                $q->where('status', true)->limit(1);
            },
            'actionPlans.actions',
        ]);

        $actionPlanModel = $structure->actionPlans->first();

        if (!$actionPlanModel) {
            return [
                'action_plan' => null,
            ];
        }

        $objectiveStart = $strategicObjective->start_date ? Carbon::parse($strategicObjective->start_date) : null;
        $objectiveEnd = $strategicObjective->end_date ? Carbon::parse($strategicObjective->end_date) : null;
        $hasObjectiveDates = $objectiveStart && $objectiveEnd;

        $actions = $actionPlanModel->actions->filter(function ($action) use ($objectiveStart, $objectiveEnd, $hasObjectiveDates) {
            if (!$hasObjectiveDates) {
                return false;
            }

            if (!$action->is_planned) {
                return false;
            }

            if (empty($action->start_date) || empty($action->end_date)) {
                return false;
            }

            $actionStart = Carbon::parse($action->start_date);
            $actionEnd = Carbon::parse($action->end_date);

            return (
                $actionStart->between($objectiveStart, $objectiveEnd) ||
                $actionEnd->between($objectiveStart, $objectiveEnd)
            );
        });

        $actionPlan = [
            'uuid'   => $actionPlanModel->uuid,
            'name'   => $actionPlanModel->name,
            'actions' => $actions->map(function ($action) use ($alreadyAlignedActionUuids) {
                return [
                    'uuid' => $action->uuid,
                    'reference' => $action->reference,
                    'name' => $action->name,
                    'start_date' => DateTimeFormatter::formatDate($action->start_date),
                    'end_date' => DateTimeFormatter::formatDate($action->end_date),
                    'status' => ActionStatus::get($action->status, app()->getLocale()),
                    'state' => ActionState::get($action->state, app()->getLocale()),
                    'is_aligned' => in_array($action->uuid, $alreadyAlignedActionUuids),
                    'actual_progress_percent' => $action->actual_progress_percent,
                    'disbursement_rate' =>  $action->total_receipt_fund > 0
                        ? round(($action->total_disbursement_fund / $action->total_receipt_fund) * 100, 2)
                        : 0
                ];
            })->values(),
        ];

        return [
            'action_plan' => $actionPlan,
        ];
    }

    /**
     * Align actions to objective.
     */
    public function align(ObjectiveAlignmentRequest $request, StrategicObjective $strategicObjective)
    {
        DB::beginTransaction();
        try {
            $userUuid = Auth::user()?->uuid;
            $alignedCount = 0;

            foreach ($request->input('lines', []) as $line) {
                $actionUuid = $line['action'];
                $identifier = $actionUuid . '-' . $strategicObjective->uuid;
                $action = Action::where('uuid', $actionUuid)->first();

                $exists = ActionObjectiveAlignment::where('identifier', $identifier)
                    ->exists();

                if (!$exists) {
                    ActionObjectiveAlignment::create([
                        'identifier' => $identifier,
                        'action_structure_uuid' => $action->structure_uuid,
                        'action_uuid' => $action->uuid,
                        'objective_structure_uuid' => $strategicObjective->structure_uuid,
                        'strategic_map_uuid' => $strategicObjective->strategic_map_uuid,
                        'strategic_element_uuid' => $strategicObjective->strategic_element_uuid,
                        'objective_uuid' => $strategicObjective->uuid,
                        'aligned_by' => $userUuid,
                        'aligned_at' => now(),
                    ]);

                    $alignedCount++;
                }
            }

            dispatch(new EvaluateStrategicObjectiveJob($strategicObjective->uuid));
            DB::commit();

            return $alignedCount;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Unalign (delete) one or more actions from an objective.
     */
    public function unalign(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $alignments = ActionObjectiveAlignment::whereIn('id', $ids)->get();

            if ($alignments->isEmpty()) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $objectiveUuids = $alignments->pluck('objective_uuid')->unique();

            ActionObjectiveAlignment::whereIn('id', $ids)->delete();

            DB::commit();

            foreach ($objectiveUuids as $objectiveUuid) {
                dispatch(new EvaluateStrategicObjectiveJob($objectiveUuid));
            }
        } catch (\RuntimeException $e) {
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
