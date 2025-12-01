<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ObjectiveAlignmentRequest;
use App\Http\Resources\ActionResource;
use App\Models\StrategicObjective;
use App\Models\Structure;
use App\Repositories\StrategicObjectiveAlignmentRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class StrategicObjectiveAlignmentController extends Controller
{
    use ApiResponse;

    private $messageSuccessAligned;
    private $messageSuccessUnaligned;
    private $repository;

    public function __construct(StrategicObjectiveAlignmentRepository $repository)
    {
        $this->messageSuccessAligned = __('app/alignment.controller.message_success_aligned');
        $this->messageSuccessUnaligned = __('app/alignment.controller.message_success_unaligned');
        $this->repository = $repository;
    }

    /**
     * List actions aligned to the given strategic objective with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActionResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActionResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Retrieve all active structures.
     */
    public function getStructures()
    {
        return response()->json(
            $this->repository->getStructures()
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Get the active action plan and actions for a structure and strategic objective.
     */
    public function getActions(Structure $structure, StrategicObjective $strategicObjective)
    {
        return response()->json(
            $this->repository->getActions($structure, $strategicObjective)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Align objectives to the given action.
     */
    public function align(ObjectiveAlignmentRequest $request, StrategicObjective $strategicObjective)
    {
        $this->repository->align($request, $strategicObjective);

        return response()->json([
            'message' => $this->messageSuccessAligned,
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove (unalign) actions from the given strategic objective.
     */
    public function unalign(Request $request)
    {
        $this->repository->unalign($request);

        return response()->json([
            'message' => $this->messageSuccessUnaligned,
        ])->setStatusCode(Response::HTTP_OK);
    }
}
