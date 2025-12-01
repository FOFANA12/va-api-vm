<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileRequest;
use App\Repositories\Auth\ProfileRepository;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    private $repository;
    private $messageSuccessUpdate;

    public function __construct(ProfileRepository $repository)
    {
        $this->repository = $repository;
        $this->messageSuccessUpdate = __('app/auth/profile.controller.message_success_update');
    }

    /**
     * Retrieve the currently logged-in user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile()
    {
        $user = $this->repository->getProfile();

        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * Update the user's profile with the provided data.
     *
     * @param ProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProfileRequest $request)
    {
        $updatedUser = $this->repository->update($request);

        return response()->json([
            'message' => $this->messageSuccessUpdate,
            'user' => $updatedUser,
        ], Response::HTTP_OK);
    }
}
