<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Repositories\ActivityStatusRepository;
use App\Support\ActivityStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityStatusController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActivityStatusRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/activity.controller.message_success_status_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of statuses for a given activity.
     */
    public function index($activityId)
    {
        return response()->json($this->repository->index($activityId))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for activity.
     */
    public function requirements(Activity $activity)
    {
        return response()->json($this->repository->requirements($activity))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created activity.
     */
    public function store(Request $request, Activity $activity)
    {
        $validStatuses = ActivityStatus::codes();

        $status = $request->input('status');

        if (!in_array($status, $validStatuses)) {
            throw new \Exception(__('app/activity.request.invalid_status'));
        }

        if (!$status) {
            throw new \Exception(__('app/activity.request.status'));
        }

        $result = $this->repository->store($request, $activity);

        return response()->json(['message' => $this->messageSuccessUpdated, 'status' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project statuses records.
     */
    public function destroy(Request $request, Activity $activity)
    {
        $status = $this->repository->destroy($request, $activity);

        return response()->json(['message' => $this->messageSuccessDeleted, 'status' => $status])->setStatusCode(Response::HTTP_OK);
    }
}
