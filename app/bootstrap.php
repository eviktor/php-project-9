<?php

use DI\Container;
use Dotenv\Dotenv;
use PageAnalyzer\Settings\Settings;
use PageAnalyzer\Settings\SettingsInterface;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/../')
        ->load();
}

$container = new Container();

$settingsData = require __DIR__ . '/../app/settings.php';
$container->set(SettingsInterface::class, function () use ($settingsData) {
    return new Settings($settingsData);
});
$settings = $container->get(SettingsInterface::class);

$container->set(Twig::class, function () use ($settings) {
    return Twig::create(
        __DIR__ . '/../templates',
        [
            'debug' => $settings->get('debug'),
            'cache' => $settings->get('cache'),
        ]
    );
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(TwigMiddleware::create($app, $container->get(Twig::class)));
$app->addRoutingMiddleware();
$app->addErrorMiddleware(
    $settings->get('displayErrorDetails'),
    $settings->get('logErrors'),
    $settings->get('logErrorDetails')
);

return $app;
