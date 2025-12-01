<?php

namespace App\Repositories;

use App\Http\Resources\ProjectStatusResource;
use App\Models\Project;
use App\Models\ProjectStatus as ModelsProjectStatus;
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
    public function index($projectId)
    {
        $query = ModelsProjectStatus::where('project_id', $projectId)
            ->orderByDesc('created_at');

        return ProjectStatusResource::collection($query->get());
    }

    /**
     * Retrieve available project statuses with localized labels.
     */
    public function requirements(Project $project)
    {
        $current = $project->status;
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
    public function store(Request $request, Project $project)
    {
        DB::beginTransaction();
        try {
            $statusCode = $request->input('status');

            $status = ModelsProjectStatus::create([
                'project_uuid' => $project->uuid,
                'project_id' => $project->id,
                'status_code' => $statusCode,
                'status_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $project->update([
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
    public function destroy(Request $request, Project $project)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $project->statuses()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastStatus = $project->statuses()->latest('created_at')->first();

            if ($lastStatus) {
                $project->update([
                    'status' => $lastStatus->status_code,
                    'status_changed_at' => $lastStatus->status_date,
                    'status_changed_by' => $lastStatus->updated_by ?? $lastStatus->created_by,
                ]);
            } else {
                $project->update([
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
