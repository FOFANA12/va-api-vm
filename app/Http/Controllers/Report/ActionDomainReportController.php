<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\ActionDomain;
use App\Repositories\Report\ActionDomainReportRepository;
use Illuminate\Http\Response;

class ActionDomainReportController extends Controller
{
    private $repository;

    public function __construct(ActionDomainReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Global reporting for a specific Action Domain.
     */
    public function globalReport(ActionDomain $actionDomain)
    {
        $report = $this->repository->getGlobalReport($actionDomain);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Generate the general dashboard for all Action Domains.
     */
    public function generalDashboard()
    {
        $report = $this->repository->buildGeneralDashboard();

        return response()->json($report, Response::HTTP_OK);
    }
}
