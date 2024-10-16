<?php

use App\Controllers\HomeController;
use App\Controllers\UrlsController;

$app->get('/', HomeController::class . ':home')
    ->setName('home');

$app->get('/urls', UrlsController::class . ':index')
    ->setName('urls.index');
$app->post('/urls', UrlsController::class . ':create')
    ->setName('urls.create');
