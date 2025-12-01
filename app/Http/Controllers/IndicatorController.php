<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndicatorRequest;
use App\Http\Resources\IndicatorResource;
use App\Models\Indicator;
use App\Repositories\IndicatorRepository;
use App\Support\IndicatorStatus;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class IndicatorController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessStatusUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(IndicatorRepository $repository)
    {
        $this->messageSuccessCreated = __('app/indicator.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/indicator.controller.message_success_updated');
        $this->messageSuccessStatusUpdated = __('app/indicator.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the indicator.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, IndicatorResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, IndicatorResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for indicator.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created indicator.
     */
    public function store(IndicatorRequest $request)
    {
        $indicator = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'indicator' => $indicator
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified indicator.
     */
    public function show(Indicator $indicator)
    {
        return response()->json($this->repository->show($indicator))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified indicator.
     */
    public function update(IndicatorRequest $request, Indicator $indicator)
    {
        $indicator = $this->repository->update($request, $indicator);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'indicator' => $indicator
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Return the list of available statuses.
     */
    public function getStatuses()
    {
        return response()->json(
            $this->repository->getStatuses()
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update only the status of a specific indicator.
     */
    public function updateStatus(Request $request, Indicator $indicator)
    {
        $validStatuses = IndicatorStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/indicator.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/indicator.request.status'));
        }

        $result = $this->repository->updateStatus($request, $indicator);

        return response()->json(['message' => $this->messageSuccessStatusUpdated, ...$result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified indicator(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
