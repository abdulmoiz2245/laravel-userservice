<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            //api respose
            return response()->json(
                [
                    'message' => 'Email already verified',
                    'data' => $user,
                    'status' => 200,
                    'response_code' => 200
                    
                ]
            );
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(
            [
                'message' => 'Email Verified',
                'data' => $user,
                'status' => 200,
                'response_code' => 200
            ]
        );
    }
}
