<?php

namespace App\Repositories;

use App\Http\Resources\ElementaryLevelStatusResource;
use App\Models\ElementaryLevelStatus as ModelsElementaryLevelStatus;
use App\Models\ElementaryLevel;
use App\Support\ElementaryLevelStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ElementaryLevelStatusRepository
{
    /**
     *  List all statuses for a given elementary level.
     */
    public function index($elementaryLevelId)
    {
        $query = ModelsElementaryLevelStatus::where('elementary_level_id', $elementaryLevelId)
            ->orderByDesc('created_at');

        return ElementaryLevelStatusResource::collection($query->get());
    }

    /**
     * Retrieve available elementary level statuses with localized labels.
     */
    public function requirements(ElementaryLevel $elementaryLevel)
    {
        $current = $elementaryLevel->status;
        $next = ElementaryLevelStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = ElementaryLevelStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new elementary level status.
     */
    public function store(Request $request, ElementaryLevel $elementaryLevel)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsElementaryLevelStatus::create([
                'elementary_level_uuid' => $elementaryLevel->uuid,
                'elementary_level_id' => $elementaryLevel->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $elementaryLevel->update([
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            DB::commit();

            return new ElementaryLevelStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple elementary level statuses.
     */
    public function destroy(Request $request, ElementaryLevel $elementaryLevel)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $elementaryLevel->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $elementaryLevel->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $elementaryLevel->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $elementaryLevel->update([
                    'status' => null,
                    'status_changed_at' => null,
                    'status_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ElementaryLevelStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
