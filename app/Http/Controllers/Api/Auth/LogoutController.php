<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiSuccessResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request): ApiSuccessResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new ApiSuccessResponse([] ,[
            'message' => 'Successfully logged out'
        ]);
    }
}
