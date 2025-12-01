<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UserRequest;
use App\Http\Resources\Settings\UserResource;
use App\Models\User;
use App\Repositories\Settings\UserRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    use ApiResponse;

    private $messageSuccessCreated;
    private $messageSuccessUpdated;
    private $messageSuccessDeleted;
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->messageSuccessCreated = __('app/settings/user.controller.message_success_created');
        $this->messageSuccessUpdated = __('app/settings/user.controller.message_success_updated');
        $this->messageSuccessDeleted = __('app/common.controller.message_success_deleted');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $result = $this->repository->index($request);
        if ($result instanceof LengthAwarePaginator) {
            return $this->respondWithPagination($result, UserResource::class)->setStatusCode(Response::HTTP_OK);
        }

        return $this->respondWithCollection($result, UserResource::class)
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Requirements data for user.
     */
    public function requirements()
    {
        return response()->json($this->repository->requirements())->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Store a newly created user.
     */
    public function store(UserRequest $request)
    {
        $user = $this->repository->store($request);

        return response()->json([
            'message' => $this->messageSuccessCreated,
            'user' => $user
        ])->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json($this->repository->show($user))->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified user.
     */
    public function update(UserRequest $request, User $user)
    {
        $user = $this->repository->update($request, $user);

        return response()->json([
            'message' => $this->messageSuccessUpdated,
            'user' => $user
        ])->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified user(s).
     */
    public function destroy(Request $request)
    {
        $this->repository->destroy($request);

        return response()->json([
            'message' => $this->messageSuccessDeleted
        ])->setStatusCode(Response::HTTP_OK);
    }
}
