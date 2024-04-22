<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::find($request->route('id'));
    
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => 404,
                'response_code' => 404
            ], 404);
        }
    
        $verificationCode = $request->route('hash');
        if ($verificationCode !== $user->verification_code) {
            return response()->json([
                'message' => 'Invalid verification code',
                'status' => 400,
                'response_code' => 400
            ], 400);
        }
    
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified',
                'data' => $user,
                'status' => 200,
                'response_code' => 200
            ]);
        }
    
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Mail::to($user->email)->send(new WelcomeMail($user->name));
        }
        return response()->json([
            'message' => 'Email Verified',
            'data' => $user,
            'status' => 200,
            'response_code' => 200
        ],200 );
    }

    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|string|max:255'
        ]);

        $emailExists = User::where('email', $request->email)->exists();
        if( !$emailExists ) {
            return response()->json(['message' =>
            "Your email does not exist in our system. Please sign up to create an account.",
            'email_exists' => $emailExists,
            'status' => 200,
            'response_code' => 200
        ], 200);
        }

        return response()->json([
            'email_exists' => $emailExists,
            'status' => 400,
            'response_code' => 400
        ],400);
    }
}
