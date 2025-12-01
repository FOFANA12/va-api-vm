<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Repositories\ProjectStateRepository;
use App\Support\ProjectState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProjectStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/project.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given project.
     */
    public function index($projectId)
    {
        return response()->json($this->repository->index($projectId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for project.
     */
    public function requirements(Project $project)
    {
        return response()->json($this->repository->requirements($project))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request, Project $project)
    {
        $validStates = ProjectState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/project.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/project.request.state'));
        }

        $result = $this->repository->store($request, $project);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project states records.
     */
    public function destroy(Request $request, Project $project)
    {
        $state = $this->repository->destroy($request, $project);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
