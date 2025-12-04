<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CapabilityDomain;
use App\Repositories\CapabilityDomainStatusRepository;
use App\Support\CapabilityDomainStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CapabilityDomainStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(CapabilityDomainStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/capability_domain.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given Capability domain.
     */
    public function index($capabilityDomainId)
    {
        return response()->json($this->repository->index($capabilityDomainId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for capability domain.
     */
    public function requirements(CapabilityDomain $capabilityDomain)
    {
        return response()->json($this->repository->requirements($capabilityDomain))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created capability domain.
     */
    public function store(Request $request, CapabilityDomain $capabilityDomain)
    {
        $validStatuses = CapabilityDomainStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/capability_domain.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/capability_domain.request.status'));
        }

        $result = $this->repository->store($request, $capabilityDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project statuses records.
     */
    public function destroy(Request $request, CapabilityDomain $capabilityDomain)
    {
        $status = $this->repository->destroy($request, $capabilityDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
