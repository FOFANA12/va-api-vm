<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use App\Models\Supplier;
use App\Repositories\ContractRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ContractController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ContractRepository $repository)
    {
        $this->messageSuccessCreated = __('app/contract.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/contract.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of the contracts.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ContractResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ContractResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Store a newly created contract.
     */
    public function store(ContractRequest $request, Supplier $supplier)
    {
        $contract = $this->repository->store($request, $supplier);
        return response()->json(['message' => $this->messageSuccessCreated, 'contract' => $contract])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified contract.
     */
    public function show(Contract $contract)
    {
        return response()->json($this->repository->show($contract))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified contract.
     */
    public function update(ContractRequest $request, Contract $contract)
    {
       $contract = $this->repository->update($request, $contract);
        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'contract' => $contract
        ])->setStatusCode(Response::HTTP_OK);
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
