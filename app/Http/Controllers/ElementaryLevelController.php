<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ElementaryLevelRequest;
use App\Http\Resources\ElementaryLevelResource;
use App\Models\ElementaryLevel;
use App\Repositories\ElementaryLevelRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ElementaryLevelController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ElementaryLevelRepository $repository)
    {
        $this->messageSuccessCreated = __('app/elementary_level.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/elementary_level.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the elementary levels.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ElementaryLevelResource::class)
                        ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ElementaryLevelResource::class)
                    ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for elementary level form.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created elementary level.
     */
    public function store(ElementaryLevelRequest $request)
    {
        $elementaryLevel = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'elementary_level' => $elementaryLevel
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified elementary level.
     */
    public function show(ElementaryLevel $elementaryLevel)
    {
        return response()->json($this->repository->show($elementaryLevel))
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified elementary level.
     */
    public function update(ElementaryLevelRequest $request, ElementaryLevel $elementaryLevel)
    {
        $elementaryLevel = $this->repository->update($request, $elementaryLevel);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'elementary_level' => $elementaryLevel
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified elementary level(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
