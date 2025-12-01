<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierController extends Controller
{
    use ApiResponse;
    
    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(SupplierRepository $repository)
    {
        $this->messageSuccessCreated = __('app/employee.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/employee.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the supplier.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, SupplierResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, SupplierResource::class)
        ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for supplier.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created supplier.
     */
    public function store(SupplierRequest $request)
    {
        $supplier = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'supplier' => $supplier
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified supplier.
     */
    public function show(Supplier $supplier)
    {
        return response()->json($this->repository->show($supplier))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified supplier.
     */
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier = $this->repository->update($request, $supplier);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'supplier' => $supplier
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified supplier(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
