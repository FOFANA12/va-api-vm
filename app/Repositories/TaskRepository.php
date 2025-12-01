<?php

namespace App\Repositories;

use App\Helpers\Utils;
use App\Models\Task;
use App\Models\ActionPhase;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Action;
use App\Models\Structure;
use App\Models\User;
use App\Support\TaskPriority;
use Illuminate\Support\Facades\Auth;

class TaskRepository
{

    /**
     * Load requirements data
     */
    public function requirements(Action $action)
    {
        $structure = $action->structure;

        $structureUuids = Utils::getStructureAndParents($structure);
        $users = User::select('uuid', 'name', 'email')
            ->where('status', true)
            ->whereHas('employee', function ($q) use ($structureUuids) {
                $q->whereIn('structure_uuid', $structureUuids);
            })
            ->orderBy('name')
            ->get();

        $priorities =  collect(TaskPriority::all())->map(function ($item) {
            return [
                'code' => $item['code'],
                'color' => $item['color'],
                'name' => $item['name'][app()->getLocale()] ?? $item['name']['fr'],
            ];
        });

        return [
            'users' => $users,
            'priorities' => $priorities,
        ];
    }

    /**
     * Show a specific task.
     */
    public function show(Task $task)
    {
        $task->loadMissing(['phase', 'assignedTo']);

        return [
            'task' => new TaskResource($task),
        ];
    }

    /**
     * Create a new task.
     */
    public function store(TaskRequest $request, ActionPhase $actionPhase)
    {
        $request->merge([
            'is_completed' => filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN),
            'created_by' => Auth::user()?->uuid,
            'updated_by' => Auth::user()?->uuid,
        ]);

        $task = $actionPhase->tasks()->create($request->only([
            'title',
            'description',
            'priority',
            'start_date',
            'end_date',
            'assigned_to',
            'deliverable',
            'is_completed',
            'created_by',
            'updated_by',
        ]));

        $task->load([
            'phase',
            'assignedTo',
        ]);

        return (new TaskResource($task))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Update a task.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $request->merge([
            'is_completed' => filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN),
            'updated_by' => Auth::user()?->uuid,
        ]);

        $task->fill($request->only([
            'title',
            'description',
            'priority',
            'start_date',
            'end_date',
            'assigned_to',
            'deliverable',
            'is_completed',
            'updated_by',
        ]));

        $task->save();

        $task->load([
            'phase',
            'assignedTo',
        ]);

        return (new TaskResource($task))->additional([
            'mode' => $request->input('mode', 'view')
        ]);
    }

    /**
     * Delete a single task.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
        } catch (\Throwable $e) {
            if ((string) $e->getCode() === "23000") {
                throw new \Exception(__('app/common.repository.foreignKey'));
            }

            throw new \Exception(__('app/common.repository.error'));
        }
    }

    /**
     * If the task is completed, it becomes incomplete and vice versa.
     */
    public function toggleCompletion(Task $task)
    {
        $task->is_completed = !$task->is_completed;
        $task->updated_by = Auth::user()?->uuid;
        $task->save();

        return $task;
    }
}
