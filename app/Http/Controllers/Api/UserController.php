<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    //

    /**
     * Get User
     *
     * @param mixed $request Request
     * 
     * @return void
     */
    public function index()
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not found',
                    'data' => null,
                    'status' => false,
                    'response_code' => 404
                ]
            );
        }
        return response()->json(
            [
                'message' => 'User found',
                'data' => $user,
                'status' => 200,
                'response_code' => 200
            ]
        );
    }
        
    /**
     * Edit update
     *
     * @param mixed $request Request
     * 
     * @return void
     */
    public function update(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not found',
                    'data' => null,
                    'status' => false,
                    'response_code' => 404
                ]
            );
        }

        $input = $request->all();
        $user->fill($input)->save();
        return response()->json(
            [
                'message' => 'User updated successfully',
                'data' => $user,
                'status' => 200,
                'response_code' => 200
            ]
        );

    }
}