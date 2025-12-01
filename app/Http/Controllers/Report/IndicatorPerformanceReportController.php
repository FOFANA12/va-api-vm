<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Repositories\Report\IndicatorPerformanceReportRepository;
use Illuminate\Http\Response;

class IndicatorPerformanceReportController extends Controller
{

    private $repository;

    public function __construct(IndicatorPerformanceReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Performance reporting for a specific action.
     */
    public function report(Indicator $indicator)
    {
        $report = [
            'progress' => $this->repository->getProgressReport($indicator),
            'delay'    => $this->repository->getDelayReport($indicator),
        ];

        return response()->json($report)->setStatusCode(Response::HTTP_OK);
    }
}
