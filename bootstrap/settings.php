<?php

$isDebug = strtolower($_ENV['APP_DEBUG'] ?? '') === 'true';
$databaseUrl = parse_url($_ENV['DATABASE_URL']);

return [
    'debug' => $isDebug,
    'displayErrorDetails' => $isDebug,
    'logErrors' => $isDebug,
    'logErrorDetails' => strtolower($_ENV['LOG_ERRORS_DETAILS '] ?? '') === 'true',
    'logToOutput' => strtolower($_ENV['LOG_TO_OUTPUT'] ?? '') === 'true',
    'cache' => $isDebug ? false : __DIR__ . '/../.cache',
    'database' => [
        'driver' => $databaseUrl['scheme'] ?? 'postgresql',
        'user' => $databaseUrl['user'],
        'password' => $databaseUrl['pass'],
        'host' => $databaseUrl['host'] ?? 'localhost',
        'port' => $databaseUrl['port'] ?? 5432,
        'name' => ltrim($databaseUrl['path'], '/'),
    ]
];
