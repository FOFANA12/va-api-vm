<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\CapabilityDomain;
use App\Repositories\Report\CapabilityDomainReportRepository;
use Illuminate\Http\Response;

class CapabilityDomainReportController extends Controller
{
    private $repository;

    public function __construct(CapabilityDomainReportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Global reporting for a specific Capability Domain.
     */
    public function globalReport(CapabilityDomain $capabilityDomain)
    {
        $report = $this->repository->getGlobalReport($capabilityDomain);

        return response()->json($report, Response::HTTP_OK);
    }
}
