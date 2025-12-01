<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecisionRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\DecisionResource;
use App\Models\Decision;
use App\Repositories\DecisionRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DecisionController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(DecisionRepository $repository)
    {
        $this->messageSuccessCreated = __('app/decision.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/decision.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the decisions.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, DecisionResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, DecisionResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for decision.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created decision.
     */
    public function store(DecisionRequest $request)
    {
        $decision = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'decision' => $decision
        ])->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Display the specified decision.
     */
    public function show(Decision $decision)
    {
        return response()->json($this->repository->show($decision))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified decision.
     */
    public function update(DecisionRequest $request, Decision $decision)
    {
        $decision = $this->repository->update($request, $decision);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'decision' => $decision
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified decision(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
