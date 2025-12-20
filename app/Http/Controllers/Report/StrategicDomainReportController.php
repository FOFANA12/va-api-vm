<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\StrategicDomain;
use App\Repositories\Report\StrategicDomainReportRepository;
use Illuminate\Http\Response;

class StrategicDomainReportController extends Controller
{
    private $repository;

    public function __construct(StrategicDomainReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Global reporting for a specific Strategic Domain.
     */
    public function globalReport(StrategicDomain $strategicDomain)
    {
        $report = $this->repository->getGlobalReport($strategicDomain);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Generate the general dashboard for all Strategic Domains.
     */
    public function generalDashboard()
    {
        $report = $this->repository->buildGeneralDashboard();

        return response()->json($report, Response::HTTP_OK);
    }
}
