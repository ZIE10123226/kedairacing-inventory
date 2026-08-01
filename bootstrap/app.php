<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->statefulApi(); // Enable stateful Sanctum API
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // On Vercel (serverless), always return JSON to avoid view dependency in error handler.
        // The Vue SPA frontend handles its own error display.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (isset($_ENV['VERCEL'])) {
                return new \Illuminate\Http\JsonResponse([
                    'error_message' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'error_trace' => $e->getTraceAsString()
                ], 500);
            }
        });
        
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return true;
        });
    })->create();
