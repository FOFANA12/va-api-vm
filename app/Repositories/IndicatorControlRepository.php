<?php

namespace App\Repositories;

use App\Helpers\DateTimeFormatter;
use App\Helpers\FileHelper;
use App\Http\Requests\IndicatorControlRequest;
use App\Http\Resources\IndicatorControlResource;
use App\Jobs\EvaluateStrategicObjectiveJob;
use App\Models\Indicator;
use App\Models\IndicatorControl;
use App\Models\IndicatorPeriod;
use App\Models\StrategicObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class IndicatorControlRepository
{
    /**
     * List indicator control with pagination, filters, sorting.
     */
    public function index(Request $request)
    {
        $indicatorId = $request->input('indicatorId');
        $query = DB::table('indicator_periods')
            ->leftJoin('indicator_controls', 'indicator_controls.indicator_period_uuid', '=', 'indicator_periods.uuid')
            ->join('indicators', 'indicator_periods.indicator_uuid', '=', 'indicators.uuid')
            ->leftJoin('users', 'indicator_controls.created_by', '=', 'users.uuid')
            ->leftJoin('attachments', function ($join) {
                $join->on('attachments.attachable_id', '=', 'indicator_controls.id')
                    ->where('attachments.attachable_type', '=', IndicatorControl::tableName());
            })
            ->select(
                'indicator_controls.id as id',
                'indicator_controls.uuid',
                'indicator_controls.control_date',
                'indicator_periods.target_value',
                'indicator_controls.achieved_value',
                'indicators.unit as unit',
                'users.name as created_by',
                'indicator_periods.start_date as period_start',
                'indicator_periods.end_date as period_end',
                'indicator_periods.id as period_id',
                'attachments.id as attachment_id',
            )->where('indicators.id', $indicatorId)
            ->orderBy('indicator_periods.start_date');


        return IndicatorControlResource::collection($query->get());
    }

    /**
     * Load requirements data
     */
    public function requirements(IndicatorPeriod $indicatorPeriod)
    {
        $hasControl = $indicatorPeriod->controls()->exists();
        if ($hasControl) {
            throw new \Exception(__('app/indicator_control.controls_error.period_already_controlled'));
        }

        $indicator = $indicatorPeriod->indicator;

        $isInProgress = $indicator->status === 'in_progress';
        $canControl = (bool) $indicator->is_planned && $isInProgress;

        if (! $canControl) {
            throw new \Exception(__('app/indicator_control.controls_error.not_eligible'));
        }

        $previousUncontrolled = IndicatorPeriod::where('indicator_uuid', $indicator->uuid)
            ->where('target_value', '<',  $indicatorPeriod->target_value)
            ->whereDoesntHave('controls')
            ->exists();

        if ($previousUncontrolled) {
            throw new \Exception(__('app/indicator_control.controls_error.previous_not_controlled'));
        }


        return [
            'indicator' => [
                'uuid' => $indicator->uuid,
                'status' => $indicator->status,
                'is_planned' => (bool) $indicator->is_planned,
                'is_in_progress' => $isInProgress,
                'can_control' => $canControl,
            ],
            'period' => [
                'id' => $indicatorPeriod->id,
                'uuid' => $indicatorPeriod->uuid,
                'start_date' => $indicatorPeriod->start_date,
                'end_date' => $indicatorPeriod->end_date,
                'target_value' => $indicatorPeriod->target_value,
                'label' => sprintf(
                    '%s → %s (VC : %s)',
                    DateTimeFormatter::formatDate($indicatorPeriod->start_date),
                    DateTimeFormatter::formatDate($indicatorPeriod->end_date),
                    $indicatorPeriod->target_value ?? 0
                ),
            ],
        ];
    }

    /**
     * Store a new indicator control.
     */
    public function store(IndicatorControlRequest $request)
    {
        $identifier = null;
        DB::beginTransaction();
        try {

            $indicatorPeriod = IndicatorPeriod::where('uuid', $request->input('indicator_period'))->firstOrFail();

            $request->merge([
                'indicator_period_uuid' => $request->input('indicator_period'),
                'target_value' => $indicatorPeriod->target_value,
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $indicatorControl = IndicatorControl::create($request->only([
                'indicator_period_uuid',
                'root_cause',
                'target_value',
                'achieved_value',
                'control_date',
                'created_by',
                'updated_by'
            ]));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $identifier = FileHelper::upload($file, 'uploads');

                $indicatorControl->attachment()->create([
                    'title' => $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'identifier' => $identifier,
                    'size' => $file->getSize(),
                    'uploaded_by' => Auth::user()->uuid,
                    'uploaded_at' => now(),
                ]);
            }

            $period = $indicatorControl->indicatorPeriod;
            $period->achieved_value = $request->input('achieved_value');
            $period->save();

            $this->updateIndicatorProgress($period->indicator);

            $indicatorControl->load([
                'indicatorPeriod',
                'attachment',
            ]);

            DB::commit();

            dispatch(new EvaluateStrategicObjectiveJob($period->indicator->strategic_objective_uuid));

            return (new IndicatorControlResource($indicatorControl))->additional([
                'mode' => $request->input('mode', 'view')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show a specific indicator control.
     */
    public function show(IndicatorControl $indicatorControl)
    {
        return ['indicator_control' => new IndicatorControlResource($indicatorControl->load([
            'indicatorPeriod',
            'attachment',
        ]))];
    }


    /**
     * Delete indicator control(s).
     */
    public function destroy(IndicatorControl $indicatorControl)
    {
        $period = $indicatorControl->indicatorPeriod;
        $indicator = $period->indicator;
        $fileIdentifier = $indicatorControl->attachment;

        DB::beginTransaction();
        try {
            $indicatorControl->delete();

            $period->update([
                'achieved_value' => 0,
            ]);

            if ($indicator) {
                $this->updateIndicatorProgress($indicator);
                dispatch(new EvaluateStrategicObjectiveJob($indicator->strategic_objective_uuid));
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

    protected function updateObjectiveState(StrategicObjective $strategicObjective)
    {
        $worstState = $strategicObjective->indicators
            ->where('state', '!=', 'none')
            ->min('state');

        $strategicObjective->update([
            'state' => $worstState ?? 'none',
        ]);
    }

    protected function updateIndicatorProgress(Indicator $indicator)
    {
        $lastControl = IndicatorControl::whereHas('indicatorPeriod', function ($q) use ($indicator) {
            $q->where('indicator_uuid', $indicator->uuid);
        })
            ->orderByDesc('control_date')
            ->orderByDesc('id')
            ->first();

        $achieved = $lastControl?->achieved_value ?? 0;
        $state = 'none';

        if ($lastControl) {
            $period = $lastControl->indicatorPeriod;
            $target = $lastControl->target_value ?? 0;

            if ($period && $period->isLast()) {
                $state = $achieved < $target ? 'risk' : 'achieved';
            } else {
                if ($achieved < ($target * 0.25)) {
                    $state = 'risk';
                } elseif ($achieved < $target) {
                    $state = 'delayed';
                } else {
                    $state = 'on_track';
                }
            }
        }

        $indicator->update([
            'achieved_value' => $achieved,
            'state' => $state,
        ]);

        if ($indicator->strategicObjective) {
            $this->updateObjectiveState($indicator->strategicObjective);
        }
    }
}
