<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionControlRequest;
use App\Models\ActionControl;
use App\Models\ActionPeriod;
use App\Repositories\ActionControlRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActionControlController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionControlRepository $repository)
    {
        $this->messageSuccessCreated = __('app/action_control.controller.message_success_created');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the action controls.
     */
    public function index(Request $request)
    {
        return response()->json($this->repository->index($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action control.
     */
    public function requirements(ActionPeriod $actionPeriod)
    {
        return response()->json($this->repository->requirements($actionPeriod))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action control.
     */
    public function store(ActionControlRequest $request, ActionPeriod $actionPeriod)
    {
        $actionControl = $this->repository->store($request, $actionPeriod);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'action_control' => $actionControl
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified action control.
     */
    public function show(ActionControl $actionControl)
    {
        return response()->json(
            $this->repository->show($actionControl)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified action control(s).
     */
    public function destroy(ActionControl $actionControl)
    {
        $this->repository->destroy($actionControl);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
