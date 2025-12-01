<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StrategicStakeholderRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\StrategicStakeholderResource;
use App\Models\StrategicStakeholder;
use App\Models\StrategicMap;
use App\Repositories\StrategicStakeholderRepository;

class StrategicStakeholderController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicStakeholderRepository $repository)
    {
        $this->messageSuccessCreated = __('app/strategic_stakeholder.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/strategic_stakeholder.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of strategic stakeholders.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicStakeholderResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicStakeholderResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic stakeholder.
     */
    public function store(StrategicStakeholderRequest $request, StrategicMap $strategicMap)
    {
        $strategicStakeholder = $this->repository->store($request, $strategicMap);
        return response()->json(['message' => $this->messageSuccessCreated, 'strategic_stakeholder' => $strategicStakeholder])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified strategic stakeholder.
     */
    public function show(StrategicStakeholder $strategicStakeholder)
    {
        return response()->json($this->repository->show($strategicStakeholder))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified strategic stakeholder.
     */
    public function update(StrategicStakeholderRequest $request, StrategicStakeholder $strategicStakeholder)
    {
        $strategicStakeholder = $this->repository->update($request, $strategicStakeholder);
        return response()->json(['message' => $this->messageSuccessUpdated, 'strategic_stakeholder' => $strategicStakeholder])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple strategic stakeholders.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])
            ->setStatusCode(Response::HTTP_OK);
    }
}
