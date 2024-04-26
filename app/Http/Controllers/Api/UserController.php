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
        if($user){
            $user->image = asset('images/profile'). '/' . $user->image;
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
                ],
                404
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
            ],
            200
        );

    }


    public function profilePicUpload(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => false,
                    'response_code' => 404
                ], 404);
            }
            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Failed',
                    'errors' => $validator->errors(),
                    'status' => false,
                    'response_code' => 422
                ], 422);
            }
    
            $oldProfileImg = $user->image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move('images/profile', $fileName);
            
                $user->image = $fileName;
                if ($oldProfileImg && file_exists(public_path('images/profile/' . $oldProfileImg))) {
                    unlink(public_path('images/profile/' . $oldProfileImg));
                }
            }
            $user->save();
            
            if ($user->image) {
                $user->image = asset('images/profile') . '/' . $user->image;
            }
            return response()->json([
                'message' => 'User updated successfully',
                'data' => $user,
                'status' => true,
                'response_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
                'status' => false,
                'response_code' => 500
            ], 500);
        }
    }
}