<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionPlanningRequest;
use App\Models\Action;
use App\Repositories\ActionPlanningRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class ActionPlanningController extends Controller
{
    use ApiResponse;

    private $messageSuccessUpdated;
    private $repository;

    public function __construct(ActionPlanningRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/action_planning.controller.message_success_updated');
        $this->repository = $repository;
    }

    /**
     * Requirements data for action planning.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified action.
     */
    public function show(Action $action)
    {
        return response()->json($this->repository->show($action))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified action planning.
     */
    public function update(ActionPlanningRequest $request, Action $action)
    {
        $actionPlanning = $this->repository->update($request, $action);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'action_planning' => $actionPlanning
        ])->setStatusCode(Response::HTTP_OK);
    }
}
