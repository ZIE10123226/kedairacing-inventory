<?php
/**
 * Forward Vercel requests to normal Laravel routing
 */

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
