<?php

namespace App\Repositories;

use App\Http\Resources\StrategicObjectiveStatusResource;
use App\Models\IndicatorStatus as ModelsIndicatorStatus;
use App\Models\StrategicObjective;
use App\Models\StrategicObjectiveStatus as ModelsStrategicObjectiveStatus;
use App\Support\IndicatorStatus;
use App\Support\StrategicObjectiveStatus;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StrategicObjectiveStatusRepository
{
    /**
     *  List all statuses for a given objective.
     */
    public function index($objectiveId)
    {
        $query = ModelsStrategicObjectiveStatus::where('strategic_objective_id', $objectiveId)
            ->orderByDesc('created_at');

        return StrategicObjectiveStatusResource::collection($query->get());
    }

    /**
     * Retrieve available indicator statuses with localized labels.
     */
    public function requirements(StrategicObjective $objective)
    {
        $current = $objective->status;
        $next = StrategicObjectiveStatus::next($current);

        if (!$this->canStopObjective($objective)) {
            $next = array_values(array_filter(
                $next,
                fn($code) => $code !== StrategicObjectiveStatus::STOPPED
            ));
        }

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = StrategicObjectiveStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new objective status.
     */
    public function store(Request $request, StrategicObjective $objective)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            if (
                $statusCode === StrategicObjectiveStatus::STOPPED
                && !$this->canStopObjective($objective)
            ) {
                throw new \Exception(__('app/strategic_objective.request.invalid_status'));
            }

            $status = ModelsStrategicObjectiveStatus::create([
                'strategic_objective_uuid' => $objective->uuid,
                'strategic_objective_id' => $objective->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $objective->status = $status->status_code;
            $objective->status_changed_at = $status->status_date;
            $objective->status_changed_by = $status->created_by;

            // Pas de mise à jour updated_at
            $objective->timestamps = false;
            $objective->save();

            if (in_array($statusCode, [
                StrategicObjectiveStatus::CLOSED,
                StrategicObjectiveStatus::STOPPED
            ])) {
                $this->propagateStatusToIndicators($objective, $statusCode, $status->status_date);
            }

            $this->updateObjectiveState($objective);

            DB::commit();

            return new StrategicObjectiveStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function canStopObjective(StrategicObjective $objective): bool
    {
        $userStructureUuid = Auth::user()?->employee?->structure_uuid;

        return $userStructureUuid !== null
            && $userStructureUuid === $objective->structure_uuid;
    }


    /**
     * Delete multiple objective statuses.
     */
    public function destroy(Request $request, StrategicObjective $objective)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $objective->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $objective->statuses()->latest('status_date')->first();

            if (!$lastStatus) {
                $objective->status = null;
                $objective->status_changed_at = null;
                $objective->status_changed_by = null;
            } else {
                $objective->status = $lastStatus->status_code;
                $objective->status_changed_at = $lastStatus->status_date;
                $objective->status_changed_by = $lastStatus->updated_by ?? $lastStatus->created_by;
            }

            $objective->timestamps = false;
            $objective->save();

            DB::commit();

            return new StrategicObjectiveStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function updateObjectiveState(StrategicObjective $objective)
    {
        $states = $objective->indicators()
            ->where('state', '!=', 'none')
            ->pluck('state');

        $worstState = $states
            ->sortBy(fn($state) => StrategicObjectiveStatus::severity($state))
            ->first();

        $objective->update([
            'state' => $worstState ?? 'none',
        ]);
    }

    protected function propagateStatusToIndicators(StrategicObjective $objective, string $statusCode, string $statusDate): void
    {
        $indicators = $objective->indicators()->get();

        foreach ($indicators as $indicator) {
            $this->applyIndicatorStatusEffects($indicator, $statusCode, $statusDate);

            ModelsIndicatorStatus::create([
                'indicator_uuid' => $indicator->uuid,
                'indicator_id' => $indicator->id,
                'status_code' => $statusCode,
                'status_date' => $statusDate,
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $indicator->status = $statusCode;
            $indicator->status_changed_at = $statusDate;
            $indicator->status_changed_by = Auth::user()?->uuid;
            $indicator->timestamps = false;
            $indicator->save();
        }
    }

    protected function applyIndicatorStatusEffects($indicator, string $statusCode, string $statusDate): void
    {
        switch ($statusCode) {
            case IndicatorStatus::STOPPED:
                $indicator->actual_end_date = null;
                break;

            case IndicatorStatus::CLOSED:
                $indicator->actual_end_date = $statusDate;
                break;
        }
    }
}
