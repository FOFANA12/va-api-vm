<?php

namespace App\Repositories;

use App\Http\Resources\ProjectStateResource;
use App\Models\Project;
use App\Models\ProjectState as ModelsProjectState;
use App\Support\ProjectState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectStateRepository
{
    /**
     * List all states for a given project.
     */
    public function index($projectId)
    {
        $query = ModelsProjectState::where('project_id', $projectId)
            ->orderByDesc('created_at');

        return ProjectStateResource::collection($query->get());
    }

    /**
     * Retrieve available project states with localized labels.
     */
    public function requirements(Project $project)
    {
        $current = $project->state;
        $next = ProjectState::next($current);

        return [
            'states' => collect($next)->map(function ($code) {
                $state = ProjectState::get($code, app()->getLocale());
                return [
                    'code'  => $state->code,
                    'name'  => $state->label,
                    'color' => $state->color,
                ];
            })->values(),
        ];
    }

    /**
     * Create (record) a new project state.
     */
    public function store(Request $request, Project $project)
    {
        DB::beginTransaction();
        try {
            $stateCode = $request->input('state');

            $state = ModelsProjectState::create([
                'project_uuid' => $project->uuid,
                'project_id' => $project->id,
                'state_code' => $stateCode,
                'state_date' => now(),
                'created_by' => Auth::user()?->uuid,
                'updated_by' => Auth::user()?->uuid,
            ]);

            $project->update([
                'state' => $state->state_code,
                'state_changed_at' => $state->state_date,
                'state_changed_by' => $state->created_by,
            ]);

            DB::commit();

            return new ProjectStateResource($state);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete multiple project states.
     */
    public function destroy(Request $request, Project $project)
    {
        $ids = $request->input('ids');

        if (empty($ids) || !is_array($ids)) {
            throw new \InvalidArgumentException(__('app/common.destroy.invalid_ids'));
        }

        DB::beginTransaction();
        try {
            $deleted = $project->states()->whereIn('id', $ids)->delete();

            if ($deleted === 0) {
                throw new \RuntimeException(__('app/common.destroy.no_items_deleted'));
            }

            $lastState = $project->states()->latest('created_at')->first();

            if ($lastState) {
                $project->update([
                    'state' => $lastState->state_code,
                    'state_changed_at' => $lastState->state_date,
                    'state_changed_by' => $lastState->updated_by ?? $lastState->created_by,
                ]);
            } else {
                $project->update([
                    'state' => null,
                    'state_changed_at' => null,
                    'state_changed_by' => null,
                ]);
            }

            DB::commit();
            
            return new ProjectStateResource($lastState);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
