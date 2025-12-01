<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Repositories\Report\DashboardReportRepository;
use Illuminate\Http\Response;

class DahsboardReportController extends Controller
{

    private $repository;

    public function __construct(DashboardReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * General dashboard reporting for a specific structure.
     */
    public function general(?Structure $structure = null)
    {
        if ($structure) {
            $targetStructure = $structure;
        } elseif (auth()->user()?->employee?->structure) {
            $targetStructure = auth()->user()->employee->structure;
        }else {
            return response()->json([
                'message' => 'No structure available for this user.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $report = $this->repository->getGeneralDahsboard($targetStructure);

        return response()->json($report, Response::HTTP_OK);
    }
}
