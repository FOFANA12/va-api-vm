<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StrategicDomain;
use App\Repositories\StrategicDomainStatusRepository;
use App\Support\StrategicDomainStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StrategicDomainStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicDomainStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/strategic_domain.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given strategic domain.
     */
    public function index($strategicDomainId)
    {
        return response()->json($this->repository->index($strategicDomainId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic domain.
     */
    public function requirements(StrategicDomain $strategicDomain)
    {
        return response()->json($this->repository->requirements($strategicDomain))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic domain.
     */
    public function store(Request $request, StrategicDomain $strategicDomain)
    {
        $validStatuses = StrategicDomainStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/strategic_domain.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/strategic_domain.request.status'));
        }

        $result = $this->repository->store($request, $strategicDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified StrategicDomainStatusRepository statuses records.
     */
    public function destroy(Request $request, StrategicDomain $strategicDomain)
    {
        $status = $this->repository->destroy($request, $strategicDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
