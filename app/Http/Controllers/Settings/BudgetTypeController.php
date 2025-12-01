<?php

namespace App\Http\Controllers\Settings;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\BudgetTypeRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\Settings\BudgetTypeResource;
use App\Models\BudgetType;
use App\Repositories\Settings\BudgetTypeRepository;

class BudgetTypeController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(BudgetTypeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/budget_type.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/budget_type.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }
    /**
     * Display a listing of budget type.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, BudgetTypeResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, BudgetTypeResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created budget type.
     */
    public function store(BudgetTypeRequest $request)
    {
        $budgetType = $this->repository->store($request);
        return response()->json(['message' => $this->messageSuccessCreated, 'budget_type' => $budgetType])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified budget type.
     */
    public function show(BudgetType $budget_type) 
    {
        return response()->json($this->repository->show($budget_type))->setStatusCode(Response::HTTP_OK);
    }
    /**
    * Update the specified budget type.
    */
    public function update(BudgetTypeRequest $request, BudgetType $budget_type) 
    {
        $budgetType = $this->repository->update($request, $budget_type);
        return response()->json(['message' => $this->messageSuccessUpdated, 'budget_type' => $budgetType]);
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
