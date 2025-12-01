<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\Auth\LoginRepository;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    private $repository;

    public function __construct(LoginRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Authenticate the user via API and return a Bearer token.
     */
    public function apiLogin(LoginRequest $request)
    {
        $data = $this->repository->apiLogin($request);

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Authenticate the user for SPA (web browser).
     */
    public function spaLogin(LoginRequest $request)
    {
        $data = $this->repository->spaLogin($request);

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Log out the user via API and revoke their tokens.
     */
    public function apiLogout()
    {
        $message = $this->repository->apiLogout();

        return response()->json(['message' => $message], Response::HTTP_OK);
    }

    /**
     * Log out the user from SPA (web browser).
     */
    public function spaLogout()
    {
        $message = $this->repository->spaLogout();

        return response()->json(['message' => $message], Response::HTTP_OK);
    }
}
