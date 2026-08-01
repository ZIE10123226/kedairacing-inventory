<?php

/**
 * Vercel Entry Point for Laravel
 * Creates /tmp directories FIRST before any Laravel code runs.
 */

// Step 1: Create all required /tmp directories immediately
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
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Step 2: Force environment variables for Vercel BEFORE autoload
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_STORAGE']        = $tmpStorage;
$_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_SERVER['APP_STORAGE']        = $tmpStorage;
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
putenv('APP_STORAGE=' . $tmpStorage);

// Step 3: Load Laravel
try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/../vendor/autoload.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($tmpStorage);

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<h1>Error</h1><pre>';
    echo htmlspecialchars($e->getMessage()) . "\n\n";
    // Show previous exception if exists
    if ($prev = $e->getPrevious()) {
        echo "Caused by: " . htmlspecialchars($prev->getMessage()) . "\n\n";
    }
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}
