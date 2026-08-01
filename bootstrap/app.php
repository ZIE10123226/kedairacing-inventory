<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Vercel: redirect storage to /tmp (writable filesystem)
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $tmpStorage = '/tmp/storage';
    $dirs = [
        $tmpStorage . '/app/public',
        $tmpStorage . '/framework/cache/data',
        $tmpStorage . '/framework/sessions',
        $tmpStorage . '/framework/testing',
        $tmpStorage . '/framework/views',
        $tmpStorage . '/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
    }
    $_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
    putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
}

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
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
