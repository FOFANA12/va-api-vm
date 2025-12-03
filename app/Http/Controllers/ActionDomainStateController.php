<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionDomain;
use App\Repositories\ActionDomainStateRepository;
use App\Support\ActionDomainState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActionDomainStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionDomainStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/action_domain.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given program.
     */
    public function index($actionDomainId)
    {
        return response()->json($this->repository->index($actionDomainId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action domain.
     */
    public function requirements(ActionDomain $actionDomain)
    {
        return response()->json($this->repository->requirements($actionDomain))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action domain.
     */
    public function store(Request $request, ActionDomain $actionDomain)
    {
        $validStates = ActionDomainState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/action_domain.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/action_domain.request.state'));
        }

        $result = $this->repository->store($request, $actionDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified program states records.
     */
    public function destroy(Request $request, ActionDomain $actionDomain)
    {
        $state = $this->repository->destroy($request, $actionDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
