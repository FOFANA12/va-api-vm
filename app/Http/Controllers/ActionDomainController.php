<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionDomainRequest;
use App\Http\Resources\ActionDomainResource;
use App\Models\ActionDomain;
use App\Repositories\ActionDomainRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ActionDomainController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionDomainRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action_domain.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/action_domain.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the action domains.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionDomainResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionDomainResource::class)
        ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action domain.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action domain.
     */
    public function store(ActionDomainRequest $request)
    {
        $actionDomain = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'action_domain' =>  $actionDomain])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified action domain.
     */
    public function show(ActionDomain $actionDomain)
    {
        return response()->json($this->repository->show($actionDomain))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified action domain.
     */
    public function update(ActionDomainRequest $request, ActionDomain $actionDomain)
    {
        $actionDomain = $this->repository->update($request, $actionDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'action_domain' =>  $actionDomain])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified action domain(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
