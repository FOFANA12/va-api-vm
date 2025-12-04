<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StrategicDomainRequest;
use App\Http\Resources\StrategicDomainResource;
use App\Models\StrategicDomain;
use App\Repositories\StrategicDomainRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class StrategicDomainController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(StrategicDomainRepository $repository)
    {
        $this->messageSuccessCreated = __('app/strategic_domain.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/strategic_domain.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the strategic domain.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, StrategicDomainResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, StrategicDomainResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for strategic domain.
     */
    public function requirements(Request $request)
    {
        return response()->json($this->repository->requirements($request))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created strategic domain.
     */
    public function store(StrategicDomainRequest $request)
    {
        $strategicDomain = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'strategic_domain' =>  $strategicDomain])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified strategic domain.
     */
    public function show(StrategicDomain $strategicDomain)
    {
        return response()->json($this->repository->show($strategicDomain))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified strategic domain.
     */
    public function update(StrategicDomainRequest $request, StrategicDomain $strategicDomain)
    {
        $strategicDomain = $this->repository->update($request, $strategicDomain);

        return response()->json(['message' => $this->messageSuccessUpdated, 'strategic_domain' =>  $strategicDomain])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified strategic domain(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
