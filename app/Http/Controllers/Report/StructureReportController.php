<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Report\StructurePerformanceReportRepository;
use App\Repositories\Report\StructureReportRepository;
use Illuminate\Http\Response;

class StructureReportController extends Controller
{

    private $performanceRepository;
    private $structureReportRepository;

    public function __construct(StructureReportRepository $structureReportRepository, StructurePerformanceReportRepository $performanceRepository)
    {
        $this->structureReportRepository = $structureReportRepository;
        $this->performanceRepository = $performanceRepository;
    }

    /**
     * Requirements data.
     */
    public function requirements()
    {
        return response()->json($this->structureReportRepository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Performance reporting for a specific structure.
     */
    public function performance(Structure $structure)
    {
        $report = $this->performanceRepository->getReport($structure);

        return response()->json($report, Response::HTTP_OK);
    }
}
