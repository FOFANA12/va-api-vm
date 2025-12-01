<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Repositories\ActivityRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ActivityRepository $repository)
    {
        $this->messageSuccessCreated = __('app/activity.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/activity.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the activities.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ActivityResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ActivityResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for activity form.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created activity.
     */
    public function store(ActivityRequest $request)
    {
        $activity = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'activity' => $activity
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity)
    {
        return response()->json($this->repository->show($activity))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified activity.
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $activity = $this->repository->update($request, $activity);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'activity' => $activity
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified activity(ies).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
