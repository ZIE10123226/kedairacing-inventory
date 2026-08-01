<?php

try {
    require __DIR__ . '/../vendor/autoload.php';

    // Set Vercel-specific paths BEFORE bootstrapping
    $storagePath = '/tmp/storage';
    putenv("APP_STORAGE={$storagePath}");
    putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
    $_ENV['APP_STORAGE'] = $storagePath;
    $_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";

    $dirs = [
        $storagePath . '/app/public',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/testing',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($storagePath);

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Real Error Found:</h1><pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
