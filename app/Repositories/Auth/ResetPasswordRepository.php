<?php

namespace App\Repositories\Auth;

use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordRepository
{
    public function resetPassword(ResetPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $request->input('email'))
                ->where('status', true)
                ->firstOrFail();

            $passwordReset = PasswordResetToken::where('email', $user->email)
                ->where('token', $request->input('token'))
                ->where('created_at', '>', now()->subMinutes(config('auth.passwords.users.expire', 60)))
                ->firstOrFail();

            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            $passwordReset->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
