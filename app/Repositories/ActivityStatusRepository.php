<?php

namespace App\Repositories;

use App\Http\Resources\ActivityStatusResource;
use App\Models\Activity;
use App\Models\ActivityStatus as ModelsActivityStatus;
use App\Support\ActivityStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ActivityStatusRepository
{
    /**
     *  List all statuses for a given activity.
     */
    public function index($activityId)
    {
        $query = ModelsActivityStatus::where('activity_id', $activityId)
            ->orderByDesc('created_at');

        return ActivityStatusResource::collection($query->get());
    }

    /**
     * Retrieve available activity statuses with localized labels.
     */
    public function requirements(Activity $activity)
    {
        $current = $activity->status;
        $next = ActivityStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = ActivityStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new project status.
     */
    public function store(Request $request, Activity $activity)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsActivityStatus::create([
                'activity_uuid' => $activity->uuid,
                'activity_id' => $activity->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $activity->update([
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            DB::commit();

            return new ActivityStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple activity statuses.
     */
    public function destroy(Request $request, Activity $activity)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $activity->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $activity->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $activity->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $activity->update([
                    'status' => null,
                    'status_changed_at' => null,
                    'status_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ActivityStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
