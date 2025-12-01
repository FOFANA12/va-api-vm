<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecisionStatusRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\DecisionStatusResource;
use App\Models\Decision;
use App\Models\DecisionStatus;
use App\Repositories\DecisionStatusRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DecisionStatusController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(DecisionStatusRepository $repository)
    {
        $this->messageSuccessCreated = __('app/decision_status.controller.message_success_created');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the status decisions.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, DecisionStatusResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, DecisionStatusResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for decision.
     */
    public function requirements(Decision $decision)
    {
        return response()->json($this->repository->requirements($decision))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a new decision status and update the current status of the decision.
     */
    public function store(DecisionStatusRequest $request, Decision $decision)
    {
        $result = $this->repository->store($request, $decision);

        return response()->json(['message' => $this->messageSuccessCreated, ...$result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified decision status.
     */
    public function show(DecisionStatus $decisionStatus)
    {
        return response()->json($this->repository->show($decisionStatus))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified or multiple decision statuses.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
