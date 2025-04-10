<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ExpiryMiddleware;
use App\Http\Middleware\ProfileMiddleware;
use App\Http\Middleware\EmailverifyMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',

        using: function () {
            $centralDomains = config('tenancy.central_domains');

            foreach ($centralDomains as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }

            foreach ($centralDomains as $domain) {
                Route::prefix('api')
                    ->domain($domain)
                    ->middleware('api')
                    ->group(base_path('routes/api.php'));
            }


            // Route::middleware('web')->group(base_path('routes/tenant.php'));
            Route::middleware('api')->group(base_path('routes/tenant.php'));
        }

    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // 'verify' => EmailverifyMiddleware::class,
            'verified_by_admin' => ProfileMiddleware::class,
            'isAdmin' => AdminMiddleware::class,
            // 'sub' => ExpiryMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
