<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorPlanningRequest;
use App\Models\Indicator;
use App\Repositories\IndicatorPlanningRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class IndicatorPlanningController extends Controller
{
    use ApiResponse;

    private $messageSuccessUpdated;
    private $repository;

    public function __construct(IndicatorPlanningRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/indicator_planning.controller.message_success_updated');
        $this->repository = $repository;
    }

    /**
     * Requirements data for indicator planning.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified indicator.
     */
    public function show(Indicator $indicator)
    {
        return response()->json($this->repository->show($indicator))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified indicator planning.
     */
    public function update(IndicatorPlanningRequest $request, Indicator $indicator)
    {
        $indicatorPlanning = $this->repository->update($request, $indicator);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'indicator_planning' => $indicatorPlanning
        ])->setStatusCode(Response::HTTP_OK);
    }
}
