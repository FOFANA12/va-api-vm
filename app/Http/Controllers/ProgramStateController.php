<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Repositories\ProgramStateRepository;
use App\Support\ProgramState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProgramStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProgramStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/program.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given program.
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
        $validStates = ProgramState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/program.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/program.request.state'));
        }

        $result = $this->repository->store($request, $program);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified program states records.
     */
    public function destroy(Request $request, Program $program)
    {
        $state = $this->repository->destroy($request, $program);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
