<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\RoleRequest;
use App\Http\Resources\Settings\RoleResource;
use App\Models\Role;
use App\Repositories\Settings\RoleRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/role.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/role.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the roles.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, RoleResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, RoleResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Return required dependencies for role forms.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created role.
     */
    public function store(RoleRequest $request)
    {
        $role = $this->repository->store($request);

        return response()->json(['message' => $this->messageSuccessCreated, 'role' =>  $role])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        return response()->json($this->repository->show($role))->setStatusCode(Response::HTTP_OK);
    }


    /**
     * Update the specified role.
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role = $this->repository->update($request, $role);

        return response()->json(['message' => $this->messageSuccessUpdated, 'role' =>  $role])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified role(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json(['message' => $this->messageSuccessDeleted])->setStatusCode(Response::HTTP_OK);
    }
}
