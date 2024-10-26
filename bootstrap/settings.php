<?php

use App\Settings\Settings;
use App\Settings\SettingsInterface;
use Nyholm\Dsn\DsnParser;

return function (): SettingsInterface {
    $isDebug = strtolower($_ENV['APP_DEBUG'] ?? '') === 'true';

    $settingsData = [
        'env' => strtolower($_ENV['APP_ENV'] ?? 'production'),
        'debug' => $isDebug,

        'displayErrorDetails' => strtolower($_ENV['LOG_ERRORS_DETAILS'] ?? '') === 'true',
        'logErrors' => false,
        'logErrorDetails' => false,

        'cache' => false, //$isDebug ? false : __DIR__ . '/../.cache',

        'databaseDsn' => DsnParser::parse($_ENV['DATABASE_URL']),

        'logger' => [
            'name' => $_ENV['LOGGER_NAME'] ?? 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => Monolog\Level::fromName($_ENV['LOGGER_LEVEL'] ?? 'error'),
        ],
    ];

    return new Settings($settingsData);
};
