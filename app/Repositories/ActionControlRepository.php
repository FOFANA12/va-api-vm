<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Helpers\FileHelper;
use App\Http\Requests\ActionControlRequest;
use App\Http\Resources\ActionControlResource;
use App\Jobs\EvaluateActionJob;
use App\Jobs\UpdateActionProgressJob;
use App\Models\ActionControl;
use App\Models\ActionControlPhase;
use App\Models\ActionPeriod;
use App\Models\ActionPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class ActionControlRepository
{
    public function index(Request $request)
    {
        $actionId = $request->input('actionId');
        $query = DB::table('action_periods')
            ->leftJoin('action_controls', 'action_controls.action_period_uuid', '=', 'action_periods.uuid')
            ->leftJoin('users', 'action_controls.created_by', '=', 'users.uuid')
            ->join('actions', 'action_periods.action_uuid', '=', 'actions.uuid')
            ->leftJoin('attachments', function ($join) {
                $join->on('attachments.attachable_id', '=', 'action_controls.id')
                    ->where('attachments.attachable_type', '=', ActionControl::tableName());
            })
            ->select(
                'action_periods.id as period_id',
                'action_periods.uuid as period_uuid',
                'action_periods.start_date as period_start',
                'action_periods.end_date as period_end',
                'action_periods.progress_percent',

                'action_controls.id',
                'action_controls.uuid',
                'action_controls.control_date',
                'action_controls.actual_progress_percent',
                'users.name as created_by',
                'attachments.id as attachment_id',
            )->where('actions.id', $actionId)
            ->orderBy('action_periods.start_date');


        return ActionControlResource::collection($query->get());
    }

    /**
     * Load requirements data
     */
    public function requirements(ActionPeriod $actionPeriod)
    {
        $hasControl = $actionPeriod->controls()->exists();
        if ($hasControl) {
            throw new \Exception(__('app/action_control.controls_error.period_already_controlled'));
        }

        $action = $actionPeriod->action;

        $isInProgress = $action->status === 'in_progress';
        $canControl = (bool) $action->is_planned && $isInProgress;

        if (! $canControl) {
            throw new \Exception(__('app/action_control.controls_error.not_eligible'));
        }

        $previousUncontrolled = ActionPeriod::where('action_uuid', $action->uuid)
            ->where('progress_percent', '<', $actionPeriod->progress_percent)
            ->whereDoesntHave('controls')
            ->exists();

        if ($previousUncontrolled) {
            throw new \Exception(__('app/action_control.controls_error.previous_not_controlled'));
        }

        $lastControl = ActionControl::whereHas('actionPeriod', function ($q) use ($action) {
            $q->where('action_uuid', $action->uuid);
        })
            ->with('controlPhases')
            ->latest('control_date')
            ->first();

        $previousValues = [];
        if ($lastControl) {
            foreach ($lastControl->controlPhases as $cp) {
                $previousValues[$cp->phase_uuid] = $cp->progress_percent;
            }
        }

        $phases = $action->phases()
            ->select('uuid', 'name', 'start_date', 'end_date', 'number', 'weight')
            ->orderBy('number')
            ->get()
            ->map(function ($phase) use ($previousValues) {
                return [
                    'uuid' => $phase->uuid,
                    'name' => $phase->name,
                    'number' => $phase->number,
                    'start_date' => DateTimeFormatter::formatDate($phase->start_date),
                    'end_date' => DateTimeFormatter::formatDate($phase->end_date),
                    'weight' => (float) $phase->weight,
                    'progress_percent' => $previousValues[$phase->uuid] ?? null,
                    'label' => sprintf(
                        'Phase %d: %s (%s → %s, %s%%)',
                        $phase->number,
                        $phase->name,
                        DateTimeFormatter::formatDate($phase->start_date),
                        DateTimeFormatter::formatDate($phase->end_date),
                        $phase->weight
                    ),
                ];
            });

        $progressValues = collect(range(0, 100, 5));

        return [
            'action' => [
                'uuid' => $action->uuid,
                'status' => $action->status,
                'is_planned' => (bool) $action->is_planned,
                'is_in_progress' => $isInProgress,
                'can_control' => $canControl,
            ],
            'period' => [
                'id' => $actionPeriod->id,
                'uuid' => $actionPeriod->uuid,
                'start_date' => $actionPeriod->start_date,
                'end_date' => $actionPeriod->end_date,
                'progress_percent' => $actionPeriod->progress_percent,
                'label' => sprintf(
                    '%s → %s (%s%%)',
                    DateTimeFormatter::formatDate($actionPeriod->start_date),
                    DateTimeFormatter::formatDate($actionPeriod->end_date),
                    $period->progress_percent ?? 0
                ),
            ],
            'phases' => $phases,
            'progress_values' => $progressValues,
        ];
    }

    /**
     * Store a new action control.
     */
    public function store(ActionControlRequest $request, ActionPeriod $actionPeriod)
    {
        $identifier = null;
        DB::beginTransaction();
        try {
            $request->merge([
                'action_period_uuid' => $actionPeriod->uuid,
                'mode' => $request->input('mode', 'view'),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionControl = ActionControl::create($request->only([
                'action_period_uuid',
                'root_cause',
                'control_date',
                'created_by',
                'updated_by'
            ]));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $actionControl->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $actualProgress = 0;

            foreach ($request->input('items', []) as $item) {
                $actionPhase = ActionPhase::where('uuid', $item['phase'])->first();

                ActionControlPhase::create(
                    [
                        'action_control_uuid' => $actionControl->uuid,
                        'phase_uuid' => $actionPhase->uuid,
                        'progress_percent' => $item['progress_percent'],
                        'weight' => $actionPhase->weight,
                    ]
                );

                $actualProgress +=  $actionPhase->weight * $item['progress_percent'];
            }

            $actualProgressPercent = round($actualProgress, 2);

            $actionPeriod = $actionControl->actionPeriod;
            $actionControl->forecast_percent = $actionPeriod->progress_percent;
            $actionControl->actual_progress_percent = $actualProgressPercent;
            $actionControl->save();

            $actionPeriod->actual_progress_percent = $actualProgressPercent;
            $actionPeriod->save();

            $actionControl->load([
                'attachment',
                'actionPeriod',
                'controlPhases.phase',
            ]);

            dispatch(new UpdateActionProgressJob($actionPeriod->action_uuid));
            dispatch(new EvaluateActionJob($actionPeriod->action_uuid));

            DB::commit();

            return (new ActionControlResource($actionControl))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if (!empty($identifier)) {
                FileHelper::delete("uploads/{$identifier}");
            }
            throw $e;
        }
    }

    /**
     * Show a specific action.
     */
    public function show(ActionControl $actionControl)
    {
        return ['action_control' => new ActionControlResource($actionControl->load([
            'attachment',
            'actionPeriod',
            'controlPhases.phase',
        ]))];
    }


    /**
     * Delete action(s).
     */
    public function destroy(ActionControl $actionControl)
    {
        $period = $actionControl->actionPeriod;
        $action = $period->action;

        $fileIdentifier = $actionControl->attachment;

        DB::beginTransaction();
        try {

            $actionControl->delete();

            $period->update([
                'actual_progress_percent' => 0,
            ]);

            if ($action) {
                dispatch(new UpdateActionProgressJob($action->uuid));
                dispatch(new EvaluateActionJob($action->uuid));
            }

            DB::commit();

            if ($fileIdentifier) {
                FileHelper::delete("uploads/{$fileIdentifier->identifier}");
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
