<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
use App\Http\Controllers\Api\Tenants\BlogPostController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use App\Http\Controllers\Api\Tenants\Auth\AuthController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware(['api'])->group(function () {
    // Route::middleware(['web', InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class,])->group(function () {
    Route::group(['prefix' => '/{tenant}', 'middleware' => [InitializeTenancyByPath::class],], function () {

        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
        });

        Route::prefix('api')->group(function () {
            Route::prefix('auth')->group(function () {
                Route::controller(AuthController::class)->group(function () {
                    Route::post('register', 'register');
                    Route::post('login', 'login');
                    Route::post('password/forgot', 'sendResetLinkEmail');
                    Route::post('password/reset', 'reset')->name('password-reset');
                    Route::post('/email/verify', 'show')->name('verify-email');
                    Route::post('resend-verification-mail', 'resendVerificationMail');
                    Route::middleware(['auth:sanctum', 'verified_by_admin'])->group(function () {
                        Route::post('logout', 'logout');
                    });
                });
            });



            Route::get('blog-posts', [BlogPostController::class, 'index']);  // Get all blog posts
            Route::get('blog-posts/{id}', [BlogPostController::class, 'show']);

            Route::middleware(['auth:sanctum', 'verified_by_admin'])->group(function () {
                Route::post('blog-posts', [BlogPostController::class, 'store']); // Create a new blog post
                Route::put('blog-posts/{id}', [BlogPostController::class, 'update']);  // Update a specific blog post
                Route::delete('blog-posts/{id}', [BlogPostController::class, 'destroy']);  // Delete a specific blog post
            });
        });
    });
});
