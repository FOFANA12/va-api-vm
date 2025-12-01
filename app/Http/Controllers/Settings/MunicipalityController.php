<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\MunicipalityRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\MunicipalityResource;
use App\Models\Municipality;
use App\Repositories\Settings\MunicipalityRepository;

class MunicipalityController extends Controller
{
     use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(MunicipalityRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/municipality.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/municipality.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of the municipalities.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, MunicipalityResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, MunicipalityResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for municipality.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created municipality.
     */
    public function store(MunicipalityRequest $request)
    {
        $municipality = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'department' => $municipality])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified municipality.
     */
    public function show(Municipality $municipality)
    {
        return response()->json($this->repository->show($municipality))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified municipality.
     */
    public function update(MunicipalityRequest $request, Municipality $municipality)
    {
        $this->repository->update($request, $municipality);
        return response()->json(['message' => $this->messageSuccessUpdated])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);
        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
