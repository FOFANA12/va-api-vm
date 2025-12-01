<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MatrixPeriodRequest;
use App\Models\MatrixPeriod;
use App\Models\StrategicMap;
use App\Repositories\MatrixPeriodRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MatrixPeriodController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessObjectivesAttached;
    private $messageSuccessObjectivesdetached;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(MatrixPeriodRepository $repository)
    {
        $this->messageSuccessCreated = __('app/matrix_period.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/matrix_period.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');

        $this->messageSuccessObjectivesAttached = __('app/matrix_period.controller.message_success_objectives_attached');
        $this->messageSuccessObjectivesdetached = __('app/matrix_period.controller.message_success_objectives_detached');

        $this->repository = $repository;
    }

    /**
     * Get all matrix periods of a strategic map.
     */
    public function getMatrixPeriods(StrategicMap $strategicMap)
    {
        return response()->json(
            $this->repository->getMatrixPeriods($strategicMap),
            Response::HTTP_OK
        );
    }

    /**
     * Get available StrategicObjectives
     */
    public function availableObjectives(StrategicMap $strategicMap, ?MatrixPeriod $matrixPeriod = null)
    {
        return response()->json(
            $this->repository->availableObjectives($strategicMap, $matrixPeriod),
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created matrix period.
     */
    public function store(MatrixPeriodRequest $request, StrategicMap $strategicMap)
    {
        $period = $this->repository->store($request, $strategicMap);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'matrix_period' => $period
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified matrix period.
     */
    public function show(MatrixPeriod $matrixPeriod)
    {
        return response()->json(
            $this->repository->show($matrixPeriod),
            Response::HTTP_OK
        );
    }

    /**
     * Update the specified matrix period.
     */
    public function update(MatrixPeriodRequest $request, MatrixPeriod $matrixPeriod)
    {
        $period = $this->repository->update($request, $matrixPeriod);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'matrix_period' => $period
        ], Response::HTTP_OK);
    }

    /**
     * Delete one matrix period.
     */
    public function destroy(MatrixPeriod $matrixPeriod)
    {
        $this->repository->destroy($matrixPeriod);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Attach strategic objectives to a matrix period.
     */
    public function attachObjectives(Request $request, MatrixPeriod $matrixPeriod)
    {
        $period = $this->repository->attachObjectives($matrixPeriod, $request);

        return response()->json([
            'message' => $this->messageSuccessObjectivesAttached,
            'matrix_period' => $period
        ], Response::HTTP_OK);
    }

    /**
     * Detach a specific objective from a matrix period.
     */
    public function detachObjective(MatrixPeriod $matrixPeriod, string $objectiveUuid)
    {
        $this->repository->detachObjective($matrixPeriod, $objectiveUuid);

        return response()->json([
            'message' => $this->messageSuccessObjectivesdetached
        ])->setStatusCode(Response::HTTP_OK);
    }
}
