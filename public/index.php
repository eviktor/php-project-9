<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();

// Set view in Container
$container->set(Twig::class, function () {
    return Twig::create(
        __DIR__ . '/../templates',
        ['cache' => false ] //__DIR__ . '/../cache']
    );
});

// Create App from container
$app = AppFactory::createFromContainer($container);

// Add Twig-View Middleware
$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

// Add other middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Render from template file templates/profile.html.twig
$app->get('/', function ($request, $response) {
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'index.html.twig');
})->setName('index');

// Run app
$app->run();
