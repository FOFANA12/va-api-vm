<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProjectOwnerRequest;
use App\Http\Resources\Settings\ProjectOwnerResource;
use App\Models\ProjectOwner;
use App\Repositories\Settings\ProjectOwnerRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectOwnerController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProjectOwnerRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/project_owner.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/project_owner.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of owners.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ProjectOwnerResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ProjectOwnerResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Fetch requirements for owner creation/update.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created owner.
     */
    public function store(ProjectOwnerRequest $request)
    {
        $projectOwner = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'project_owner' => $projectOwner
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified owner.
     */
    public function show(ProjectOwner $projectOwner)
    {
        return response()->json(
            $this->repository->show($projectOwner)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified owner.
     */
    public function update(ProjectOwnerRequest $request, ProjectOwner $projectOwner)
    {   
        $projectOwner = $this->repository->update($request, $projectOwner);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'project_owner' => $projectOwner
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple owners.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
