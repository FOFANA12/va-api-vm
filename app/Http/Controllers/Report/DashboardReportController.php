<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Report\DashboardReportRepository;
use Illuminate\Http\Response;

class DashboardReportController extends Controller
{
    private DashboardReportRepository $repository;

    public function __construct(DashboardReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Resolve target structure (route param or user structure)
     */
    private function resolveStructure(?Structure $structure = null): Structure
    {
        if ($structure) {
            return $structure;
        }

        if (auth()->user()?->employee?->structure) {
            return auth()->user()->employee->structure;
        }

        abort(Response::HTTP_BAD_REQUEST,
            'No structure available for this user.'
        );
    }

    /**
     * General dashboard
     */
    public function general(?Structure $structure = null)
    {
        $targetStructure = $this->resolveStructure($structure);

        $report = $this->repository->getGeneralDahsboard($targetStructure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Strategic dashboard
     */
    public function strategic(?Structure $structure = null)
    {
        $targetStructure = $this->resolveStructure($structure);

        $report = $this->repository->getStrategicDashboard($targetStructure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Operational dashboard
     */
    public function operational(?Structure $structure = null)
    {
        $targetStructure = $this->resolveStructure($structure);

        $report = $this->repository->getOperationalDashboard($targetStructure);

        return response()->json($report, Response::HTTP_OK);
    }

    /**
     * Financial dashboard
     */
    public function financial(?Structure $structure = null)
    {
        $targetStructure = $this->resolveStructure($structure);

        $report = $this->repository->getFinancialDashboard($targetStructure);

        return response()->json($report, Response::HTTP_OK);
    }
}
