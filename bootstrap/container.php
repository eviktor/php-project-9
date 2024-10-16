<?php

use DI\Container;
use Dotenv\Dotenv;
use Slim\Views\Twig;
use App\Settings\SettingsInterface;

if (file_exists(__DIR__ . '/../.env')) {
    Dotenv::createImmutable(__DIR__ . '/../')
        ->load();
}

$container = new Container();

$getSettings = require __DIR__ . '/../bootstrap/settings.php';
$container->set(SettingsInterface::class, $getSettings);

$container->set(Twig::class, function (SettingsInterface $settings) {
    return Twig::create(
        __DIR__ . '/../templates',
        [
            'debug' => $settings->get('debug'),
            'cache' => $settings->get('cache'),
        ]
    );
});

$getPDO = require __DIR__ . '/../bootstrap/database.php';
$container->set(\PDO::class, $getPDO);

return $container;
