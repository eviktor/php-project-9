<?php

use App\Controllers\HomeController;
use App\Controllers\UrlController;

$app->get('/', HomeController::class . ':home')
    ->setName('home');

$app->get('/urls', UrlController::class . ':index')
    ->setName('urls.index');
$app->post('/urls', UrlController::class . ':create')
    ->setName('urls.create');
$app->get('/urls/{id}', UrlController::class . ':show')
    ->setName('urls.show');
