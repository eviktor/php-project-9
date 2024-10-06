<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$container->set(Twig::class, function () {
    return Twig::create(
        __DIR__ . '/../templates',
        ['cache' => false ] //__DIR__ . '/../cache']
    );
});

$app = AppFactory::createFromContainer($container);
$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function ($request, $response) use ($container) {
    $twig = $container->get(Twig::class);
    return $twig->render($response, 'index.html.twig');
})->setName('index');

$app->get('/urls', function ($request, $response) use ($container) {
    $twig = $container->get(Twig::class);
    return $twig->render($response, 'urls.html.twig');
})->setName('urls');

$app->run();
