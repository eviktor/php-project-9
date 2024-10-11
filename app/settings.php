<?php

return [
    'debug' => DI\env('APP_DEBUG', false),
    'displayErrorDetails' => DI\env('APP_DEBUG', false),

    'logErrors' => DI\env('LOG_ERRORS', true),
    'logErrorDetails' => DI\env('LOG_ERRORS_DETAILS', true),
    'logToOutput' => DI\env('LOG_TO_OUTPUT', false),
];
