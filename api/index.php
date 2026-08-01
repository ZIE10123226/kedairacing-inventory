<?php
/**
 * Forward Vercel requests to normal Laravel routing
 */

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Ensure /tmp/storage exists for Vercel
    $storagePath = $_ENV['APP_STORAGE'] ?? '/tmp/storage';
    $app->useStoragePath($storagePath);

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

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Real Error Found:</h1><pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
}
