<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPhaseRequest;
use App\Models\Action;
use App\Models\ActionPhase;
use App\Repositories\ActionPhaseRepository;
use Illuminate\Http\Response;

class ActionPhaseController extends Controller
{
    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $messageSuccessInitialized;
    private $repository;

    public function __construct(ActionPhaseRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action_phase.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/action_phase.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->messageSuccessInitialized = __('app/action_phase.controller.message_success_initialized');
        $this->repository = $repository;
    }

    /**
     * Display all phases for a given action.
     */
    public function index(Action $action)
    {
        return response()->json(
            $this->repository->index($action),
            Response::HTTP_OK
        );
    }

    /**
     * Requirements data for action phases.
     */
    public function requirements()
    {
        return response()->json(
            $this->repository->requirements(),
            Response::HTTP_OK
        );
    }

    /**
     * Show a specific action phase.
     */
    public function show(ActionPhase $actionPhase)
    {
        return response()->json(
            $this->repository->show($actionPhase),
            Response::HTTP_OK
        );
    }

    /**
     * Store a new action phase for the given action.
     */
    public function store(ActionPhaseRequest $request, Action $action)
    {
        $actionPhase = $this->repository->store($request, $action);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action_phase' => $actionPhase
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified action phase.
     */
    public function update(ActionPhaseRequest $request, $actionId, ActionPhase $actionPhase)
    {
        $actionPhase = $this->repository->update($request, $actionPhase);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'action_phase'   => $actionPhase
        ], Response::HTTP_OK);
    }

    /**
     * Initialize default phases for a given action.
     */
    public function initializeDefaultPhases(Action $action)
    {
        $this->repository->initializeDefaultPhases($action);

        return response()->json([
            'message' => $this->messageSuccessInitialized,
        ], Response::HTTP_OK);
    }

    /**
     * Delete one action phases.
     */
    public function destroy(ActionPhase $actionPhase)
    {
        $this->repository->destroy($actionPhase);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
