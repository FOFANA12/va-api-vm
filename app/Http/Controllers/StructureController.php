<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\StructureRequest;
use App\Http\Resources\StructureResource;
use App\Models\Structure;
use App\Repositories\StructureRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class StructureController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StructureRepository $repository)
    {
        $this->messageSuccessCreated = __('app/structure.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/structure.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the structures.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StructureResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StructureResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for structure.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created structure.
     */
    public function store(StructureRequest $request)
    {
        $structure = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'structure' =>  $structure])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified structure.
     */
    public function show(Structure $structure)
    {
        return response()->json($this->repository->show($structure))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified structure.
     */
    public function update(StructureRequest $request, Structure $structure)
    {
        $structure = $this->repository->update($request, $structure);

        return response()->json(['message' => $this->messageSuccessUpdated, 'structure' =>  $structure])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified structure(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
