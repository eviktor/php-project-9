<?php

use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../bootstrap/container.php';

AppFactory::setContainer($container);

$app = AppFactory::create();

require __DIR__ . '/../bootstrap/middleware.php';

require __DIR__ . '/../bootstrap/routes.php';

if ($_ENV['APP_ENV'] !== 'testing') {
    $app->run();
}

return $app;
