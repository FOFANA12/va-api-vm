<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionAlignmentRequest;
use App\Http\Resources\StrategicObjectiveResource;
use App\Models\Action;
use App\Repositories\ActionAlignmentRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ActionAlignmentController extends Controller
{
    use ApiResponse;

    private $messageSuccessAligned;
    private $messageSuccessUnaligned;
    private $repository;

    public function __construct(ActionAlignmentRepository $repository)
    {
        $this->messageSuccessAligned = __('app/alignment.controller.message_success_aligned');
        $this->messageSuccessUnaligned = __('app/alignment.controller.message_success_unaligned');
        $this->repository = $repository;
    }

    /**
     * List objectives aligned to the given action with pagination, filtering, and sorting.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicObjectiveResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicObjectiveResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Load requirements data (available objectives not yet aligned to the given action).
     */
    public function requirements(Action $action)
    {
        return response()->json(
            $this->repository->requirements($action)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Align objectives to the given action.
     */
    public function align(ActionAlignmentRequest $request, Action $action)
    {
        $this->repository->align($request, $action);

        return response()->json([
            'message' => $this->messageSuccessAligned,
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Remove (unalign) objectives from the given action.
     */
    public function unalign(Request $request)
    {
        $this->repository->unalign($request);

        return response()->json([
            'message' => $this->messageSuccessUnaligned,
        ])->setStatusCode(Response::HTTP_OK);
    }
}
