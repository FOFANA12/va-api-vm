<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Repositories\ProjectStatusRepository;
use App\Support\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProjectStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/project.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given project.
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
        $validStatuses = ProjectStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/project.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/project.request.status'));
        }

        $result = $this->repository->store($request, $project);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project statuses records.
     */
    public function destroy(Request $request, Project $project)
    {
        $status = $this->repository->destroy($request, $project);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
