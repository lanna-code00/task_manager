<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthSignUpFormRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signUpUser(AuthSignUpFormRequest $request)
    {
        $_user = $this->authService->signUpUser($request->validated());
        return $_user;
    }

    public function signInUser(Request $request)
    {
        $_login = $this->authService->signInUser($request->all());
        return $_login;
    }
}
