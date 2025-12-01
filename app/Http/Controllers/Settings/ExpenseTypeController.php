<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ExpenseTypeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\ExpenseTypeResource;
use App\Models\ExpenseType;
use App\Repositories\Settings\ExpenseTypeRepository;

class ExpenseTypeController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(ExpenseTypeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/expense_type.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/expense_type.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of expense types.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, ExpenseTypeResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, ExpenseTypeResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created expense type.
     */
    public function store(ExpenseTypeRequest $request)
    {
        $expenseType = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'expense_type' => $expenseType])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified expense type.
     */
    public function show(ExpenseType $expense_type) 
    {
        return response()->json($this->repository->show($expense_type))->setStatusCode(Response::HTTP_OK);
    }
    /**
    * Update the specified expense type.
    */
    public function update(ExpenseTypeRequest $request, ExpenseType $expense_type) 
    {
        $expenseType = $this->repository->update($request, $expense_type);
        return response()->json(['message' => $this->messageSuccessUpdated, 'expense_type' => $expenseType]);
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
