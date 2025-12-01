<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Action;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use App\Models\ActionPhase;
use App\Models\Task;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->messageSuccessCreated = __('app/task.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/task.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Requirements data for tasks.
     */
    public function requirements(Action $action)
    {
        return response()->json(
            $this->repository->requirements($action),
            Response::HTTP_OK
        );
    }

    /**
     * Show a specific task.
     */
    public function show(Task $task)
    {
        return response()->json(
            $this->repository->show($task),
            Response::HTTP_OK
        );
    }

    /**
     * Create a new task.
     */
    public function store(TaskRequest $request, ActionPhase $actionPhase)
    {
        $task = $this->repository->store($request, $actionPhase);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'task' => $task
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified task.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $task = $this->repository->update($request, $task);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'task'   => $task
        ], Response::HTTP_OK);
    }

    /**
     * Delete one task.
     */
    public function destroy(Task $task)
    {
        $this->repository->destroy($task);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    public function toggle(Task $task)
    {
        $updatedTask = $this->repository->toggleCompletion($task);

        $message = $updatedTask->is_completed
            ? __('app/task.controller.message_task_marked_as_completed')
            : __('app/task.controller.message_task_marked_as_in_progress');

        return response()->json(['message' => $message, 'task' => $updatedTask])->setStatusCode(Response::HTTP_OK);
    }
}
