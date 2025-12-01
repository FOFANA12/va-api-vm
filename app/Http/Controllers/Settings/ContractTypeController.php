<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ContractTypeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\ContractTypeResource;
use App\Models\ContractType;
use App\Repositories\Settings\ContractTypeRepository;

class ContractTypeController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ContractTypeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/contract_type.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/contract_type.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of the contract_types.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ContractTypeResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ContractTypeResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Store a newly created contract_type.
     */
    public function store(ContractTypeRequest $request)
    {
        $contract_type = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'contract_type' => $contract_type])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified contract_type.
     */
    public function show(ContractType $contract_type)
    {
        return response()->json($this->repository->show($contract_type))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified contract_type.
     */
    public function update(ContractTypeRequest $request, ContractType $contract_type)
    {
        $this->repository->update($request, $contract_type);
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
