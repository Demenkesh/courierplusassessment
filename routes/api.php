<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tenants\Auth\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json([
        'hello user , This is your multi-tenant application'
    ]);
});

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::get('fetchorganisation', 'fetchorganisation');
        Route::post('login', 'login');
        Route::post('password/forgot', 'sendResetLinkEmail');
        Route::post('password/reset', 'reset')->name('password-reset');
        Route::post('/email/verify', 'show')->name('verify-email');
        Route::post('resend-verification-mail', 'resendVerificationMail');
        Route::middleware(['auth:sanctum','verified_by_admin'])->group(function () {
            Route::post('logout', 'logout');
        });
    });

});



