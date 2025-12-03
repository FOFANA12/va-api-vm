<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CapabilityDomain;
use App\Repositories\ActivityStateRepository;
use App\Support\ActivityState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActivityStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/capability_domain.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given activity.
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
        $validStates = ActivityState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/capability_domain.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/capability_domain.request.state'));
        }

        $result = $this->repository->store($request, $capabilityDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project states records.
     */
    public function destroy(Request $request, CapabilityDomain $capabilityDomain)
    {
        $state = $this->repository->destroy($request, $capabilityDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
