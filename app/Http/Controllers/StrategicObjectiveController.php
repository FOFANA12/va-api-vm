<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StrategicObjectiveRequest;
use App\Http\Resources\StrategicObjectiveResource;
use App\Models\StrategicObjective;
use App\Repositories\StrategicObjectiveRepository;
use App\Support\StrategicObjectiveStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class StrategicObjectiveController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessStatusUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicObjectiveRepository $repository)
    {
        $this->messageSuccessCreated = __('app/strategic_objective.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/strategic_objective.controller.message_success_updated');
        $this->messageSuccessStatusUpdated = __('app/strategic_objective.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the strategic objective.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicObjectiveResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicObjectiveResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic objective.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic objective.
     */
    public function store(StrategicObjectiveRequest $request)
    {
        $objective = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'strategic_objective' => $objective
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Return the list of available statuses.
     */
    public function getStatuses(StrategicObjective $strategicObjective)
    {
        return response()->json(
            $this->repository->getStatuses($strategicObjective)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update only the status of a specific strategic objective.
     */
    public function updateStatus(Request $request, StrategicObjective $strategicObjective)
    {
        $validStatuses = StrategicObjectiveStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/strategic_objective.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/strategic_objective.request.status'));
        }

        $result = $this->repository->updateStatus($request, $strategicObjective);

        return response()->json(['message' => $this->messageSuccessStatusUpdated, ...$result])->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Display the specified strategic objective.
     */
    public function show(StrategicObjective $strategicObjective)
    {
        return response()->json($this->repository->show($strategicObjective))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified strategic objective.
     */
    public function update(StrategicObjectiveRequest $request, StrategicObjective $strategic_objective)
    {
        $objective = $this->repository->update($request, $strategic_objective);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'strategic_objective' => $objective
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified strategic objective(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
