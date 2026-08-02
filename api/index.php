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
    $tmpStorage . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Step 2: Force environment variables for Vercel BEFORE autoload
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_STORAGE']        = $tmpStorage;
$_ENV['APP_SERVICES_CACHE'] = $tmpStorage . '/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpStorage . '/bootstrap/cache/packages.php';
$_ENV['APP_CONFIG_CACHE']   = $tmpStorage . '/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE']   = $tmpStorage . '/bootstrap/cache/routes-v7.php';
$_ENV['APP_EVENTS_CACHE']   = $tmpStorage . '/bootstrap/cache/events.php';
$_SERVER['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_SERVER['APP_SERVICES_CACHE'] = $tmpStorage . '/bootstrap/cache/services.php';
$_SERVER['APP_PACKAGES_CACHE'] = $tmpStorage . '/bootstrap/cache/packages.php';
$_SERVER['APP_CONFIG_CACHE']   = $tmpStorage . '/bootstrap/cache/config.php';
$_SERVER['APP_ROUTES_CACHE']   = $tmpStorage . '/bootstrap/cache/routes-v7.php';
$_SERVER['APP_EVENTS_CACHE']   = $tmpStorage . '/bootstrap/cache/events.php';
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
putenv('APP_STORAGE=' . $tmpStorage);
putenv('APP_SERVICES_CACHE=' . $tmpStorage . '/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=' . $tmpStorage . '/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=' . $tmpStorage . '/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=' . $tmpStorage . '/bootstrap/cache/routes-v7.php');
putenv('APP_EVENTS_CACHE=' . $tmpStorage . '/bootstrap/cache/events.php');

// Step 3: Fix SCRIPT_NAME so Laravel resolves routes correctly on Vercel
// Vercel sets SCRIPT_NAME to /api/index.php, Laravel must see / as the base
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Step 4: Load Laravel
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
