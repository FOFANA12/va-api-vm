<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
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
        $this->messageSuccessUpdated = __('app/program.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given program.
     */
    public function index($programId)
    {
        return response()->json($this->repository->index($programId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for program.
     */
    public function requirements(Program $program)
    {
        return response()->json($this->repository->requirements($program))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request, Program $program)
    {
        $validStatuses = ProgramStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/program.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/program.request.status'));
        }

        $result = $this->repository->store($request, $program);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified program statuses records.
     */
    public function destroy(Request $request, Program $program)
    {
        $status = $this->repository->destroy($request, $program);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
