<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\IndicatorCategory;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests\Settings\IndicatorCategoryRequest;
use App\Http\Resources\Settings\IndicatorCategoryResource;
use App\Repositories\Settings\IndicatorCategoryRepository;

class IndicatorCategoryController extends Controller
{
     use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(IndicatorCategoryRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/indicator_category.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/indicator_category.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of indicator categories.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, IndicatorCategoryResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, IndicatorCategoryResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly indicator category.
     */
    public function store(IndicatorCategoryRequest $request)
    {
        $indicatorCategory = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'indicator_category' => $indicatorCategory])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified indicator category.
     */
    public function show(IndicatorCategory $indicatorCategory)
    {
        return response()->json($this->repository->show($indicatorCategory))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified indicator category.
     */
    public function update(IndicatorCategoryRequest $request, IndicatorCategory $indicatorCategory)
    {

        $indicatorCategory = $this->repository->update($request, $indicatorCategory);
        return response()->json(['message' => $this->messageSuccessUpdated, 'indicator_category' => $indicatorCategory])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove one or multiple delegated indicator categories.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
