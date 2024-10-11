<?php

use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/../')
        ->load();
}

$container = new Container();

$container->set(Twig::class, function () {
    return Twig::create(
        __DIR__ . '/../templates',
        require __DIR__ . '/../app/settings.php'
    );
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

return $app;
