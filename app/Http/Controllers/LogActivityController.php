<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogActivityResource;
use App\Models\LogActivity;
use App\Repositories\LogActivityRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class LogActivityController extends Controller
{
    use ApiResponse;

    private $messageSuccessDeleted;
    private $repository;

    public function __construct(LogActivityRepository $repository)
    {
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, LogActivityResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, LogActivityResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified activity log.
     */
    public function show(LogActivity $activity)
    {
        return response()->json(
            $this->repository->show($activity)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete one or more activity logs.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
