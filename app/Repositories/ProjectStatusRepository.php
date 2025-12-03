<?php

namespace App\Repositories;

use App\Http\Resources\ProjectStatusResource;
use App\Models\ProjectStatus as ModelsProjectStatus;
use App\Models\StrategicDomain;
use App\Support\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectStatusRepository
{
    /**
     *  List all statuses for a given project.
     */
    public function index($strategicDomainId)
    {
        $query = ModelsProjectStatus::where('strategic_domain_id', $strategicDomainId)
            ->orderByDesc('created_at');

        return ProjectStatusResource::collection($query->get());
    }

    /**
     * Retrieve available project statuses with localized labels.
     */
    public function requirements(StrategicDomain $strategicDomain)
    {
        $current = $strategicDomain->status;
        $next = ProjectStatus::next($current);

        return [
            'statuses' => collect($next)->map(function ($code) {
                $status = ProjectStatus::get($code, app()->getLocale());
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
    public function store(Request $request, StrategicDomain $strategicDomain)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsProjectStatus::create([
                'strategic_domain_uuid' => $strategicDomain->uuid,
                'strategic_domain_id' => $strategicDomain->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $strategicDomain->update([
                'status' => $status->status_code,
                'status_changed_at' => $status->status_date,
                'status_changed_by' => $status->created_by,
            ]);

            DB::commit();

            return new ProjectStatusResource($status);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Delete multiple project statuses.
     */
    public function destroy(Request $request, StrategicDomain $strategicDomain)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $strategicDomain->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $strategicDomain->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $strategicDomain->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $strategicDomain->update([
                    'status' => null,
                    'status_changed_at' => null,
                    'status_changed_by' => null,
                ]);
            }

            DB::commit();

            return new ProjectStatusResource($lastStatus);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
