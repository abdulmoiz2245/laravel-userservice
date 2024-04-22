<?php 

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;


use App\Http\Controllers\Api\VerifyEmailController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::group(['prefix'=>'user','as'=>'user.', 'middleware'=>'auth:sanctum'], function(){
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::put('/update', [UserController::class, 'update'])->name('update');
});


// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    // ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})
// ->middleware(['auth:api', 'throttle:6,1'])
->name('verification.send');