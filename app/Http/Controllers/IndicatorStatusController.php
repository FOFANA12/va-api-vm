<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Repositories\IndicatorStatusRepository;
use App\Support\IndicatorStatus;

class IndicatorStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(IndicatorStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/indicator.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given indicator.
     */
    public function index($indicatorId)
    {
        return response()->json($this->repository->index($indicatorId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for indicator.
     */
    public function requirements(Indicator $indicator)
    {
        return response()->json($this->repository->requirements($indicator))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created indicator.
     */
    public function store(Request $request, Indicator $indicator)
    {
        $validStatuses = IndicatorStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/indicator.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/indicator.request.status'));
        }

        $result = $this->repository->store($request, $indicator);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified indicator statuses records.
     */
    public function destroy(Request $request, Indicator $indicator)
    {
        $status = $this->repository->destroy($request, $indicator);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
