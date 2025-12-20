<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CapabilityDomain;
use App\Models\ElementaryLevel;
use App\Repositories\Report\ElementaryLevelReportRepository;
use Illuminate\Http\Response;

class ElementaryLevelReportController extends Controller
{
    private $repository;

    public function __construct(ElementaryLevelReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Global reporting for a specific Elementary Level.
     */
    public function globalReport(ElementaryLevel $elementaryLevel)
    {
        $report = $this->repository->getGlobalReport($elementaryLevel);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Generate the general dashboard for all Elementary Levels.
     */
    public function generalDashboard()
    {
        $report = $this->repository->buildGeneralDashboard();

        return response()->json($report, Response::HTTP_OK);
    }
}
