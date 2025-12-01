<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Repositories\Auth\ForgetPasswordRepository;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    private $messageSendEmail;
    private $messageNotSendEmail;
    private $repository;

    public function __construct(ForgetPasswordRepository $repository)
    {
        $this->messageSendEmail = __('app/auth/forget_password.controller.message_send_email');
        $this->messageNotSendEmail = __('app/auth/forget_password.controller.message_not_send_email');
        $this->repository = $repository;
    }

    /**
     * Handle password reset request by sending a email.
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        try {
            $this->repository->forgetPassword($request);

            return response()->json([
                'message' => $this->messageSendEmail,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'email' => [$this->messageNotSendEmail],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
