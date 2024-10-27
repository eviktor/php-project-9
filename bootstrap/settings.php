<?php

use App\Settings\Settings;
use App\Settings\SettingsInterface;
use Nyholm\Dsn\DsnParser;

return function (): SettingsInterface {
    $isDebug = env('APP_DEBUG', false) || env("CI", false);

    $settingsData = [
        'env' => env('APP_ENV', 'production'),
        'debug' => $isDebug,

        'displayErrorDetails' => env('LOG_ERRORS_DETAILS', $isDebug),
        'logErrors' => true,
        'logErrorDetails' => true,

        'cache' => false, //$isDebug ? false : __DIR__ . '/../.cache',

        'databaseDsn' => DsnParser::parse(env('DATABASE_URL')),

        'logger' => [
            'name' => 'slim-app',
            'path' => $isDebug ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => Monolog\Level::fromName(env('LOGGER_LEVEL', 'error')),
        ],
    ];

    return new Settings($settingsData);
};
