<?php

use Slim\Views\Twig;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../app/bootstrap.php';
$container = $app->getContainer();

$app->get('/', function ($request, $response) use ($container) {
    $twig = $container->get(Twig::class);
    return $twig->render($response, 'index.html.twig');
})->setName('index');

$app->get('/urls', function ($request, $response) use ($container) {
    $twig = $container->get(Twig::class);
    return $twig->render($response, 'urls.html.twig');
})->setName('urls');

$app->run();
