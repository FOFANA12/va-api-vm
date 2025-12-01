<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordResetToken;
use App\Repositories\Auth\ResetPasswordRepository;
use Illuminate\Http\Response;

class ResetPasswordController extends Controller
{
    private $messageSuccess;
    private $messageError;
    private $repository;

    public function __construct(ResetPasswordRepository $repository)
    {
        $this->repository = $repository;
        $this->messageSuccess = __('app/auth/reset_password.controller.message_success');
        $this->messageError = __('app/auth/reset_password.controller.message_error');
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $email = $request->input('email');
            $token = $request->input('token');

            $passwordReset = PasswordResetToken::where('email', $email)->first();

            if (!$passwordReset) {
                return response()->json([
                    'message' => __('app/auth/common.not_found_token'),
                ], Response::HTTP_NOT_FOUND);
            }

            if (!PasswordResetToken::query()->validToken($email, $token)->exists()) {
                return response()->json([
                    'message' => __('app/auth/common.token_expired'),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->repository->resetPassword($request);

            return response()->json([
                'message' => $this->messageSuccess,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $this->messageError,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
