<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Repositories\ActionStatusRepository;
use App\Support\ActionStatus;

class ActionStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActionStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/action.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given action.
     */
    public function index($actionId)
    {
        return response()->json($this->repository->index($actionId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action.
     */
    public function requirements(Action $action)
    {
        return response()->json($this->repository->requirements($action))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action.
     */
    public function store(Request $request, Action $action)
    {
        $validStatuses = ActionStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/action.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/action.request.status'));
        }

        $result = $this->repository->store($request, $action);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified action statuses records.
     */
    public function destroy(Request $request, Action $action)
    {
        $status = $this->repository->destroy($request, $action);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
