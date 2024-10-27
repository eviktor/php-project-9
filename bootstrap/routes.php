<?php

use App\Controllers\HomeController;
use App\Controllers\UrlController;
use App\Controllers\UrlCheckController;
use Slim\App;

return function (App $app) {
    $app->get('/', HomeController::class . ':home')
        ->setName('home');

    $app->post('/urls/{id:[0-9]+}/checks', UrlCheckController::class . ':create')
        ->setName('urlchecks.create');

    $app->get('/urls', UrlController::class . ':index')
        ->setName('urls.index');
    $app->post('/urls', UrlController::class . ':create')
        ->setName('urls.create');
    $app->get('/urls/{id:[0-9]+}', UrlController::class . ':show')
        ->setName('urls.show');
};
