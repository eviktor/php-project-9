<?php

use App\Controllers\HomeController;
use App\Controllers\UrlsController;

$app->get('/', HomeController::class . ':index')
    ->setName('home');

$app->get('/urls', UrlsController::class . ':index')
    ->setName('urls');
