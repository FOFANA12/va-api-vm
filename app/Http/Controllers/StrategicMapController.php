<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StrategicMapRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\StrategicMapResource;
use App\Models\StrategicMap;
use App\Repositories\StrategicMapRepository;

class StrategicMapController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicMapRepository $repository)
    {
        $this->messageSuccessCreated = __('app/strategic_map.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/strategic_map.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the structure strategic maps.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicMapResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicMapResource::class)
        ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic map.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic map.
     */
    public function store(StrategicMapRequest $request)
    {
        $structureStrategicMap = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'strategic_map' =>  $structureStrategicMap])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified strategic map.
     */
    public function show(Request $reques, StrategicMap $strategicMap)
    {
        return response()->json($this->repository->show($reques, $strategicMap))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified strategic map.
     */
    public function update(StrategicMapRequest $request, StrategicMap $strategicMap)
    {
        $structureStrategicMap = $this->repository->update($request, $strategicMap);

        return response()->json(['message' => $this->messageSuccessUpdated, 'strategic_map' =>  $structureStrategicMap])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified strategic map(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}

