<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->messageSuccessCreated = __('app/project.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/project.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ProjectResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ProjectResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for project.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created project.
     */
    public function store(ProjectRequest $request)
    {
        $project = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'project' =>  $project])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        return response()->json($this->repository->show($project))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified project.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $project = $this->repository->update($request, $project);

        return response()->json(['message' => $this->messageSuccessUpdated, 'project' =>  $project])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
