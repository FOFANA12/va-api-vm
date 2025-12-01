<?php

namespace App\Repositories\Auth;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Mail\ResetPassword;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordRepository
{
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        $token = Str::random(64);

        $this->deleteExpiredTokens();
        $passwordReset = PasswordResetToken::updateOrCreate(
            ['email' => $user->email],
            [
                'token' => $token,
            ]
        );

        if ($user && $passwordReset) {
            Mail::to($user->email)->send(new ResetPassword($user, $token));
        }
    }

    private function deleteExpiredTokens()
    {
        $expirationMinutes = config('auth.passwords.users.expire', 60);

        PasswordResetToken::where('created_at', '<', Carbon::now()->subMinutes($expirationMinutes))->delete();
    }
}
