<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierEvaluationRequest;
use App\Http\Resources\SupplierEvaluationResource;
use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Repositories\SupplierEvaluationRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierEvaluationController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(SupplierEvaluationRepository $repository)
    {
        $this->messageSuccessCreated = __('app/supplier_evaluation.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/supplier_evaluation.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of the supplier evaluations.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, SupplierEvaluationResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, SupplierEvaluationResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Store a newly created supplier evaluation.
     */
    public function store(SupplierEvaluationRequest $request, Supplier $supplier)
    {
        $evaluation = $this->repository->store($request, $supplier);
        return response()->json(['message' => $this->messageSuccessCreated, 'supplier_evaluation' => $evaluation])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified supplier evaluation.
     */
    public function show(SupplierEvaluation $supplierEvaluation)
    {
        return response()->json($this->repository->show($supplierEvaluation))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified supplier evaluation.
     */
    public function update(SupplierEvaluationRequest $request, SupplierEvaluation $supplierEvaluation)
    {
        $evaluation = $this->repository->update($request, $supplierEvaluation);
        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'supplier_evaluation' => $evaluation
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Supplier $supplier)
    {
        $result = $this->repository->destroy($request, $supplier);
        return response()->json(['message' => $this->messageSuccessDeleted, ...$result])->setStatusCode(Response::HTTP_OK);
    }
}
