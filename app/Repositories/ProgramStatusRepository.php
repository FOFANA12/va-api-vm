<?php

namespace App\Repositories;

use App\Http\Resources\ProgramStatusResource;
use App\Models\ActionDomain;
use App\Models\ProgramStatus as ModelsProgramStatus;
use App\Support\ProgramStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProgramStatusRepository
{
    /**
     *  List all statuses for a given program.
     */
    public function index($actionDomainId)
    {
        $query = ModelsProgramStatus::where('action_domain_id', $actionDomainId)
            ->orderByDesc('created_at');

        return ProgramStatusResource::collection($query->get());
    }

    /**
     * Retrieve available program statuses with localized labels.
     */
    public function requirements(ActionDomain $actionDomain)
    {
        $current = $actionDomain->status;
        $next = ProgramStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = ProgramStatus::get($code, app()->getLocale());
                return [
                    'code'  => $status->code,
                    'name'  => $status->label,
                    'color' => $status->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new program status.
     */
    public function store(Request $request, ActionDomain $actionDomain)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsProgramStatus::create([
                'action_domain_uuid' => $actionDomain->uuid,
                'action_domain_id' => $actionDomain->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $actionDomain->update([
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            DB::commit();

            return new ProgramStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple program statuses.
     */
    public function destroy(Request $request, ActionDomain $actionDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $actionDomain->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $actionDomain->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $actionDomain->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $actionDomain->update([
                    'status' => null,
                    'status_changed_at' => null,
                    'status_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ProgramStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
