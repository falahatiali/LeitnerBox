<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailVerificationRequest;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Models\User;
use Illuminate\Http\Response;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user->hasVerifiedEmail()) {
            return view('emails.verification', [
                'user' => $request->user,
                'message' => 'Account already verified',
                'type' => 'success'
            ]);
        }

        if ($request->fulfill()){
           return view('emails.verification', [
               'user' => $request->user,
               'message' => 'Your account has been successfully verified. You can now login to your account',
               'type' => 'success'
           ]);
        }

        return view('emails.verification', [
            'user' => $request->user,
            'message' => 'Email verification failed',
            'type' => 'error'
        ]);

    }
}
