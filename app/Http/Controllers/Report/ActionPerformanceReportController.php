<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Repositories\Report\ActionPerformanceReportRepository;
use Illuminate\Http\Response;

class ActionPerformanceReportController extends Controller
{

    private $repository;

    public function __construct(ActionPerformanceReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Performance reporting for a specific action.
     */
    public function report(Action $action)
    {
        $report = [
            'budget'   => $this->repository->getBudgetReport($action),
            'progress' => $this->repository->getProgressReport($action),
            'delay'    => $this->repository->getDelayReport($action),
        ];

        return response()->json($report)->setStatusCode(Response::HTTP_OK);
    }
}
