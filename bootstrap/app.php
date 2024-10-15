<?php

use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Settings\SettingsInterface;

$container = require __DIR__ . '/../bootstrap/container.php';

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

$app->addRoutingMiddleware();

$settings = $container->get(SettingsInterface::class);
$app->addErrorMiddleware(
    $settings->get('displayErrorDetails'),
    $settings->get('logErrors'),
    $settings->get('logErrorDetails')
);

require __DIR__ . '/../bootstrap/routes.php';

$app->run();
