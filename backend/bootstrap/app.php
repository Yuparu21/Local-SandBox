<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware
            ->statefulApi()
            ->api(append: [
                HandleCors::class,
            ])
            ->validateCsrfTokens(except: [
                'api/login',
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // カスタム例外ハンドラの登録
    })->create();
