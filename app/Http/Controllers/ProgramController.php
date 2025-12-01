<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Repositories\ProgramRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ProgramController extends Controller
{
    use ApiResponse;
    
    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ProgramRepository $repository)
    {
        $this->messageSuccessCreated = __('app/program.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/program.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the programs.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ProgramResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ProgramResource::class)
        ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for program.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created program.
     */
    public function store(ProgramRequest $request)
    {
        $program = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'program' =>  $program])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program)
    {
        return response()->json($this->repository->show($program))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified program.
     */
    public function update(ProgramRequest $request, Program $program)
    {
        $program = $this->repository->update($request, $program);

        return response()->json(['message' => $this->messageSuccessUpdated, 'program' =>  $program])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified program(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
