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
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        // Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));
        event(new Registered($user));
        $token = JWTAuth::fromUser($user);
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
            return response()->json([
                'message' => 'Valiation Failed',
                'data' => $validator->errors(),
                'status' => false,
            ],
            422);
        }

        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => false
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token',
                'status' => false
            ], 500);
        }


        // $user   = User::where('email', $request->email)->firstOrFail();
        // $token  = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'message'       => 'Login success',
                'access_token'  => $token,
                'token_type'    => 'Bearer'
            ]
        );
    }

    public function refreshToken(Request $request)
    {
        $token = JWTAuth::parseToken()->refresh();
        
        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'User logged out successfully',
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to log out',
                'error' => $e->getMessage(),
                'status' => false
            ], 500);
        }
    }


    public function sendCode(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
                'status' => false,
            ], 422);
        }
            $verificationCode = random_int(100000, 999999);
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => false,
                ], 404);
            }

        try {
            $user->phone = $request->phone;
            $user->phone_code = $verificationCode;
            $this->sendVerificationCode($request->phone, $verificationCode);
            $user->save();
            return response()->json([
                'message' => 'Verification code sent successfully to your phone.',
                'status' => true,
            ]);
        } catch (TwilioException $e) {
            return response()->json([
                'message' => 'Failed to send verification code.',
                'error' => $e->getMessage(),
                'status' => false,
            ], 400);
        }
    }
    

    private function sendVerificationCode(string $phoneNumber, int $verificationCode): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        $client = new Client($sid, $token);

        $client->messages->create(
            $phoneNumber,
            [
                'from' => $from,
                'body' => "Your verification code is: $verificationCode",
            ]
        );
    }

    public function verifyPhoneCode(Request $request, $id, $code)
    {
        if (!$code) {
            return response()->json([
                'message' => 'Validation Failed: Code is Required',
                'status' => false,
            ], 422);
        }
        if (strlen($code) < 6) {
            return response()->json([
                'message' => 'Validation Failed: The code must least 6 digits.',
                'status' => false,
            ], 422);
        }
        if (!is_numeric($code)) {
            return response()->json([
                'message' => 'Validation Failed: The code must be a numeric string with at least 6 digits.',
                'status' => false,
            ], 422);
        }
    
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
                'status' => false,
            ], 404);
        }
    
        if ($user->phone_verified_at) {
            return response()->json([
                'message' => 'Phone number already verified.',
                'status' => true
            ], 200);
        }
    
        if ($request->code == $user->phone_code) {
            $user->phone_verified_at = now();
            $user->save();
    
            return response()->json([
                'message' => 'Phone number verified successfully.',
                'status' => true,
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid verification code.',
                'status' => false,
            ], 400);
        }
    }
}
