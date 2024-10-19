<?php

use App\Settings\Settings;
use App\Settings\SettingsInterface;
use Nyholm\Dsn\DsnParser;

return function (): SettingsInterface {
    $isDebug = strtolower($_ENV['APP_DEBUG'] ?? '') === 'true';

    $settingsData = [
        'debug' => $isDebug,
        'displayErrorDetails' => $isDebug,
        'logErrors' => $isDebug,
        'logErrorDetails' => strtolower($_ENV['LOG_ERRORS_DETAILS '] ?? '') === 'true',
        'logToOutput' => strtolower($_ENV['LOG_TO_OUTPUT'] ?? '') === 'true',
        'cache' => $isDebug ? false : __DIR__ . '/../.cache',
        'databaseDsn' => DsnParser::parse($_ENV['DATABASE_URL']),
    ];

    return new Settings($settingsData);
};
