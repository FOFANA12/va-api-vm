<?php

namespace App\Repositories\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\UserProfileResource;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class LoginRepository
{

    public function apiLogin(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $credentials['status'] = true;

        if (! Auth::attempt($credentials)) {
            abort(Response::HTTP_UNAUTHORIZED, __('app/auth/common.failed'));
        }

        $user = Auth::guard()->user()->load(['employee.structure', 'role.permissions']);
        $tokenName = $request->input('device_name', 'WebApp');
        $token = $user->createToken($tokenName)->plainTextToken;

        AuditLogger::log(
            'User logged in via API',
            $user,
            $user,
            'login',
            ['device' => $tokenName]
        );

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserProfileResource($user),
        ];
    }

    /**
     * Handle SPA login
     *
     * @param LoginRequest $request
     * @return array<string, mixed>
     */
    public function spaLogin(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $credentials['status'] = true;

        if (! Auth::guard('web')->attempt($credentials)) {
            abort(Response::HTTP_UNAUTHORIZED, __('app/auth/common.failed'));
        }

        $user = Auth::guard('web')->user()->load(['employee.structure', 'role.permissions']);

        AuditLogger::log(
            'User logged in via SPA',
            $user,
            $user,
            'login'
        );

        return [
            'user' => new UserProfileResource($user),
        ];
    }

    /**
     * Handle API logout
     *
     * @return string
     */
    public function apiLogout()
    {
        $user = Auth::guard()->user();

        if ($user) {
            $user->currentAccessToken()?->delete();

            AuditLogger::log(
                'User logged out via API',
                $user,
                $user,
                'logout'
            );
        }

        return __('app/auth/common.logout_success');
    }

    /**
     * Handle SPA logout
     *
     * @return string
     */
    public function spaLogout()
    {
        $user = Auth::guard('web')->user();

        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        if ($user) {
            AuditLogger::log(
                'User logged out via SPA',
                $user,
                $user,
                'logout'
            );
        }

        return __('app/auth/common.logout_success');
    }
}
