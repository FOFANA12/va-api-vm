<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ElementaryLevel;
use App\Repositories\ElementaryLevelStatusRepository;
use App\Support\ElementaryLevelStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ElementaryLevelStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ElementaryLevelStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/elementary_level.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given elementary level.
     */
    public function index($elementaryLevelId)
    {
        return response()->json($this->repository->index($elementaryLevelId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for elementary level.
     */
    public function requirements(ElementaryLevel $elementaryLevel)
    {
        return response()->json($this->repository->requirements($elementaryLevel))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created elementary level.
     */
    public function store(Request $request, ElementaryLevel $elementaryLevel)
    {
        $validStatuses = ElementaryLevelStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/elementary_level.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/elementary_level.request.status'));
        }

        $result = $this->repository->store($request, $elementaryLevel);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified elementary level statuses records.
     */
    public function destroy(Request $request, ElementaryLevel $elementaryLevel)
    {
        $status = $this->repository->destroy($request, $elementaryLevel);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
