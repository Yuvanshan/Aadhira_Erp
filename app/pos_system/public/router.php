<?php

// Built-in PHP server router for Laravel.
// Serve existing files directly and otherwise route all requests to index.php.
// This is required when using php -S without Apache/Nginx rewrite rules.

if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . ($url['path'] ?? '');

    if ($file !== __FILE__ && is_file($file)) {
        return false;
    }
}

require __DIR__ . '/index.php';
