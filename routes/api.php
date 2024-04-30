<?php 

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;


use App\Http\Controllers\Api\VerifyEmailController;

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::group(['prefix' => 'user', 'as' => 'user.', 'middleware' => 'auth:api'], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::put('/update', [UserController::class, 'update'])->name('update');
        Route::patch('/profile-pic-upload', [UserController::class, 'profilePicUpload'])->name('profilePicUpload');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refreshToken');
    });

//Send phone code
Route::patch('/user/{id}/send-code', [AuthController::class, 'sendCode'])->name('sendCodeToPhone');
// Verify  user phone with phone Code
Route::get('/user/verify-phone-code/{id}/{code}', [AuthController::class, 'verifyPhoneCode'])->name('verifyPhoneCode');


// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    // ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

//Email Existence
Route::post('/email/check', [VerifyEmailController::class, 'checkEmail'])->name('email.check');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})
// ->middleware(['auth:api', 'throttle:6,1'])
->name('verification.send');