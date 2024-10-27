<?php

use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../bootstrap/container.php';

AppFactory::setContainer($container);

$app = AppFactory::create();

$addMiddleware = require __DIR__ . '/../bootstrap/middleware.php';
$addMiddleware($app, $container);

$addRoutes = require __DIR__ . '/../bootstrap/routes.php';
$addRoutes($app);

if (env('APP_ENV') !== 'testing') {
    $app->run();
}

return $app;
