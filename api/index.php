<?php

/**
 * Serverless Entry Point for Vercel Deployment
 * Sets up writable /tmp directories for Laravel views, session, cache, and logs
 */

$storageDirectories = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache'
];

foreach ($storageDirectories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Forward to Laravel's main front controller
require __DIR__ . '/../public/index.php';
