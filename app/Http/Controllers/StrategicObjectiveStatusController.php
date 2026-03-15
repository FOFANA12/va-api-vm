<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\StrategicObjective;
use App\Repositories\StrategicObjectiveStatusRepository;
use App\Support\StrategicObjectiveStatus;

class StrategicObjectiveStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicObjectiveStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/strategic_objective.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given objective.
     */
    public function index($objectiveId)
    {
        return response()
            ->json($this->repository->index($objectiveId))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for objective.
     */
    public function requirements(StrategicObjective $strategicObjective)
    {
        return response()
            ->json($this->repository->requirements($strategicObjective))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created objective status.
     */
    public function store(Request $request, StrategicObjective $strategicObjective)
    {
        $validStatuses = StrategicObjectiveStatus::codes();

        $status = $request->input('status');

        if (!$status) {
            throw new \Exception(__('app/strategic_objective.request.status'));
        }

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/strategic_objective.request.invalid_status'));
        }

        $result = $this->repository->store($request, $strategicObjective);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'status' => $result
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified objective statuses records.
     */
    public function destroy(Request $request, StrategicObjective $strategicObjective)
    {
        $status = $this->repository->destroy($request, $strategicObjective);

        return response()->json([
            'message' => $this->messageSuccessDeleted,
            'status' => $status
        ])->setStatusCode(Response::HTTP_OK);
    }
}