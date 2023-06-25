<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! auth()->attempt($credentials)) {
           return new ApiErrorResponse('Invalid credentials',401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return new ApiSuccessResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],[
            'user' => $user
        ]);
    }
}
