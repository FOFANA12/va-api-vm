<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\FundingSourceRequest;
use App\Http\Resources\Settings\FundingSourceResource;
use App\Models\FundingSource;
use App\Repositories\Settings\FundingSourceRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class FundingSourceController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(FundingSourceRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/funding_source.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/funding_source.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the funding sources.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);

        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, FundingSourceResource::class)
                ->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, FundingSourceResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for funding source.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created funding source.
     */
    public function store(FundingSourceRequest $request)
    {
        $fundingSource = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'funding_source' => $fundingSource
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified funding source.
     */
    public function show(FundingSource $fundingSource)
    {
        return response()->json(
            $this->repository->show($fundingSource)
        )->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified funding source.
     */
    public function update(FundingSourceRequest $request, FundingSource $fundingSource)
    {
        $fundingSource = $this->repository->update($request, $fundingSource);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'funding_source' => $fundingSource
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified funding source(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
