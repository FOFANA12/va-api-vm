<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorControlRequest;
use App\Http\Resources\IndicatorControlResource;
use App\Models\Indicator;
use App\Models\IndicatorControl;
use App\Models\IndicatorPeriod;
use App\Repositories\IndicatorControlRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class IndicatorControlController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(IndicatorControlRepository $repository)
    {
        $this->messageSuccessCreated = __('app/indicator_control.controller.message_success_created');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the indicator controls.
     */
    public function index(Request $request)
    {
        return response()->json($this->repository->index($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for indicator control.
     */
    public function requirements(IndicatorPeriod $indicator_period)
    {
        return response()->json($this->repository->requirements($indicator_period))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created indicator control.
     */
    public function store(IndicatorControlRequest $request)
    {
        $indicatorControl = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'indicator_control' => $indicatorControl
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified indicator control.
     */
    public function show(IndicatorControl $indicatorControl)
    {
        return response()->json(
            $this->repository->show($indicatorControl)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified indicator control.
     */
    public function destroy(IndicatorControl $indicatorControl)
    {
        $this->repository->destroy($indicatorControl);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
