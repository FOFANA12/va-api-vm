<?php

namespace App\Repositories;

use App\Http\Resources\ActivityStateResource;
use App\Models\Activity;
use App\Models\ActivityState as ModelsActivityState;
use App\Support\ActivityState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityStateRepository
{
    /**
     * List all states for a given activity.
     */
    public function index($activityId)
    {
        $query = ModelsActivityState::where('activity_id', $activityId)
            ->orderByDesc('created_at');

        return ActivityStateResource::collection($query->get());
    }

    /**
     * Retrieve available activity states with localized labels.
     */
    public function requirements(Activity $activity)
    {
        $current = $activity->state;
        $next = ActivityState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = ActivityState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new activity state.
     */
    public function store(Request $request, Activity $activity)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsActivityState::create([
                'activity_uuid' => $activity->uuid,
                'activity_id' => $activity->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $activity->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new ActivityStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple activity states.
     */
    public function destroy(Request $request, Activity $activity)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $activity->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $activity->states()->latest('created_at')->first();

            if ($lastState) {
                $activity->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $activity->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ActivityStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
