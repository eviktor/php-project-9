<?php

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use App\Settings\SettingsInterface;

$container = require __DIR__ . '/../bootstrap/container.php';

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(
    function ($request, $next) {
        // Start PHP session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Change flash message storage
        $this->get(Messages::class)->__construct($_SESSION);
        return $next->handle($request);
    }
);

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
