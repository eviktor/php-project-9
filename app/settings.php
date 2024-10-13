<?php

$isDebug = strtolower($_ENV['APP_DEBUG'] ?? '') === 'true';

return [
    'debug' => $isDebug,
    'displayErrorDetails' => $isDebug,
    'logErrors' => $isDebug,
    'logErrorDetails' => strtolower($_ENV['LOG_ERRORS_DETAILS '] ?? '') === 'true',
    'logToOutput' => strtolower($_ENV['LOG_TO_OUTPUT'] ?? '') === 'true',
    'cache' => $isDebug ? false : __DIR__ . '/../.cache',
];
