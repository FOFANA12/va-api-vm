<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CapabilityDomainRequest;
use App\Http\Resources\CapabilityDomainResource;
use App\Models\CapabilityDomain;
use App\Repositories\CapabilityDomainRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class CapabilityDomainController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(CapabilityDomainRepository $repository)
    {
        $this->messageSuccessCreated = __('app/capability_domain.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/capability_domain.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the capability domains.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, CapabilityDomainResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, CapabilityDomainResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for capability domain form.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created capability domain.
     */
    public function store(CapabilityDomainRequest $request)
    {
        $capabilityDomain = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'capability_domain' => $capabilityDomain
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified capability domain.
     */
    public function show(CapabilityDomain $capabilityDomain)
    {
        return response()->json($this->repository->show($capabilityDomain))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified capability domain.
     */
    public function update(CapabilityDomainRequest $request, CapabilityDomain $capabilityDomain)
    {
        $capabilityDomain = $this->repository->update($request, $capabilityDomain);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'capability_domain' => $capabilityDomain
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified capability domain(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
