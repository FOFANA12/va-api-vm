<?php

namespace App\Http\Controllers\Settings;

use App\Models\Stakeholder;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Settings\StakeholderRequest;
use App\Http\Resources\Settings\StakeholderResource;
use App\Repositories\Settings\StakeholderRepository;

class StakeholderController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StakeholderRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/stakeholder.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/stakeholder.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of stakeholders.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StakeholderResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StakeholderResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created stakeholder.
     */
    public function store(StakeholderRequest $request)
    {
        $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified stakeholder.
     */
    public function show(Stakeholder $stakeholder)
    {
        return response()->json($this->repository->show($stakeholder))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified stakeholder.
     */
    public function update(StakeholderRequest $request, Stakeholder $stakeholder)
    {
        $this->repository->update($request, $stakeholder);
        return response()->json(['message' => $this->messageSuccessUpdated])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple delegated stakeholders.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
