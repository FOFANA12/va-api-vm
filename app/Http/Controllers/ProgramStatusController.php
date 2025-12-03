<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActionDomain;
use App\Repositories\ProgramStatusRepository;
use App\Support\ProgramStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProgramStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/action_domain.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given program.
     */
    public function index($actionDomainId)
    {
        return response()->json($this->repository->index($actionDomainId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for action domain.
     */
    public function requirements(ActionDomain $actionDomain)
    {
        return response()->json($this->repository->requirements($actionDomain))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created action domain.
     */
    public function store(Request $request, ActionDomain $actionDomain)
    {
        $validStatuses = ProgramStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/action_domain.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/action_domain.request.status'));
        }

        $result = $this->repository->store($request, $actionDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified program statuses records.
     */
    public function destroy(Request $request, ActionDomain $actionDomain)
    {
        $status = $this->repository->destroy($request, $actionDomain);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
