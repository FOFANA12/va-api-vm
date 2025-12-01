<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\DelegatedProjectOwnerRequest;
use App\Http\Resources\Settings\DelegatedProjectOwnerResource;
use App\Models\DelegatedProjectOwner;
use App\Repositories\Settings\DelegatedProjectOwnerRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class DelegatedProjectOwnerController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(DelegatedProjectOwnerRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/delegated_project_owner.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/delegated_project_owner.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of delegated project owners.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, DelegatedProjectOwnerResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, DelegatedProjectOwnerResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Fetch requirements for delegated project owner creation/update.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created delegated project owner.
     */
    public function store(DelegatedProjectOwnerRequest $request)
    {
        $delegatedProjectOwner = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'delegated_project_owner' => $delegatedProjectOwner
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified delegated project owner.
     */
    public function show(DelegatedProjectOwner $delegatedProjectOwner)
    {
        return response()->json(
            $this->repository->show($delegatedProjectOwner)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified delegated project owner.
     */
    public function update(DelegatedProjectOwnerRequest $request, DelegatedProjectOwner $delegatedProjectOwner)
    {
        $delegatedProjectOwner = $this->repository->update($request, $delegatedProjectOwner);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'delegated_project_owner' => $delegatedProjectOwner
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple delegated project owners.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
