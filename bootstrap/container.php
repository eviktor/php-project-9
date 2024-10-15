<?php

use DI\Container;
use Dotenv\Dotenv;
use Slim\Views\Twig;
use App\Settings\Settings;
use App\Settings\SettingsInterface;

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/../')
        ->load();
}

$container = new Container();

$container->set(SettingsInterface::class, function () {
    $settingsData = require __DIR__ . '/../bootstrap/settings.php';
    return new Settings($settingsData);
});

$container->set(Twig::class, function (SettingsInterface $settings) {
    return Twig::create(
        __DIR__ . '/../templates',
        [
            'debug' => $settings->get('debug'),
            'cache' => $settings->get('cache'),
        ]
    );
});

return $container;
