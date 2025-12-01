<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeController extends Controller
{
    use ApiResponse;
    
    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(EmployeeRepository $repository)
    {
        $this->messageSuccessCreated = __('app/employee.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/employee.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, EmployeeResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, EmployeeResource::class)
        ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for employee.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created employee.
     */
    public function store(EmployeeRequest $request)
    {
        $employee = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'employee' => $employee
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        return response()->json($this->repository->show($employee))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified employee.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $employee = $this->repository->update($request, $employee);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'employee' => $employee
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified employee(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
