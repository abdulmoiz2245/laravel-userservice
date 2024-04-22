<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;


class AuthController extends Controller
{
    //

        
    /**
     * Register User
     *
     * @param mixed $request Request
     * 
     * @return void
     */
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(), 
            [
                'name'      => 'required|string|max:255',
                'email'     => 'required|string|max:255|unique:users',
                'password'  => 'required|string|min:8'
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Valiation Failed',
                    'data' => $validator->errors(),
                    'status' => false,
                ],
                422
            );
        }
        $verificationCode = random_int(100000, 999999);

        $user = User::create(
            [
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'verification_code' => $verificationCode,
            ]
        );
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));
        // event(new Registered($user));
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(
            [
                'message'       => 'User created successfully',
                'data'          => $user,
                'access_token'  => $token,
                'token_type'    => 'Bearer',
                'status'        => true,
                'response_code' => 201
            ],
            201
        );
    }

    
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'     => 'required|string|max:255',
                'password'  => 'required|string'
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $credentials    =   $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(
                [
                    'message' => 'User not found'
                ],
                401
            );
        }

        $user   = User::where('email', $request->email)->firstOrFail();
        $token  = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'message'       => 'Login success',
                'access_token'  => $token,
                'token_type'    => 'Bearer'
            ]
        );
    }

    public function logout(Request $request)
    {
        Auth::user()->tokens()->delete();
        return response()->json(
            [
                'message' => 'Logout successfull'
            ]
        );
    }
}
