<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StrategicDomain;
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
        $this->messageSuccessUpdated = __('app/strategic_domain.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given project.
     */
    public function index($strategicDomainId)
    {
        return response()->json($this->repository->index($strategicDomainId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic domain.
     */
    public function requirements(StrategicDomain $strategicDomain)
    {
        return response()->json($this->repository->requirements($strategicDomain))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic domain.
     */
    public function store(Request $request, StrategicDomain $strategicDomain)
    {
        $validStates = ProjectState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/strategic_domain.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/strategic_domain.request.state'));
        }

        $result = $this->repository->store($request, $strategicDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project states records.
     */
    public function destroy(Request $request, StrategicDomain $strategicDomain)
    {
        $state = $this->repository->destroy($request, $strategicDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
