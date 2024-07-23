<?php

use App\Http\Middleware\JsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
            ->prefix('api/v1/')
            ->group(base_path('routes/v1/task.php'));

            Route::middleware('api')
            ->prefix('api/v1/')
            ->group(base_path('routes/v1/auth.php'));
            
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(JsonResponse::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
