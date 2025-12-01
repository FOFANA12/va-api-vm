<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Repositories\ActivityStateRepository;
use App\Support\ActivityState;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityStateController extends Controller
{
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActivityStateRepository $repository)
    {
        $this->messageSuccessUpdated = __('app/activity.controller.message_success_state_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of states for a given activity.
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
        $validStates = ActivityState::codes();

        $state = $request->input('state');

        if (!in_array($state, $validStates)) {
            throw new \Exception(__('app/activity.request.invalid_state'));
        }

        if (!$state) {
            throw new \Exception(__('app/activity.request.state'));
        }

        $result = $this->repository->store($request, $activity);

        return response()->json(['message' => $this->messageSuccessUpdated, 'state' =>  $result])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified project states records.
     */
    public function destroy(Request $request, Activity $activity)
    {
        $state = $this->repository->destroy($request, $activity);

        return response()->json(['message' => $this->messageSuccessDeleted, 'state' => $state])->setStatusCode(Response::HTTP_OK);
    }
}
