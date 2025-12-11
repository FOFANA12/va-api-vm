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
    public function globalReport(ActionDomain $domain)
    {
        $report = $this->repository->getGlobalReport($domain);

        return response()->json($report, Response::HTTP_OK);
    }
}
