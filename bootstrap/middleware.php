<?php

use App\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app, ContainerInterface $container) {
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
};
