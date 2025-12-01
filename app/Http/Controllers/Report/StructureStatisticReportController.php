<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Report\StructureStatisticReportRepository;
use Illuminate\Http\Response;

class StructureStatisticReportController extends Controller
{

    private $repository;

    public function __construct(StructureStatisticReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Acquisition report for a specific structure.
     */
    public function acquisitions(Structure $structure)
    {
        $report = $this->repository->getAcquisitionReport($structure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Expense report for a specific structure.
     */
    public function expenses(Structure $structure)
    {
        $report = $this->repository->getExpenseReport($structure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Expense report grouped by strategic objectives.
     */
    public function expensesByObjective(Structure $structure)
    {
        $report = $this->repository->getExpensesByObjective($structure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Expense report grouped by strategic elements.
     */
    public function expensesByAxis(Structure $structure)
    {
        $report = $this->repository->getExpensesByAxis($structure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Expense report grouped by strategic maps.
     */
    public function expensesByMap(Structure $structure)
    {
        $report = $this->repository->getExpensesByMap($structure);

        return response()->json($report, Response::HTTP_OK);
    }
}
