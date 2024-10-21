<?php

use DI\Container;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use App\Settings\SettingsInterface;

$isTesting = (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing');
$envDir = __DIR__ . '/../' . ($isTesting ? 'tests/' : '');
$envFile = $isTesting ? '.env.testing' : '.env';
if (file_exists("$envDir$envFile")) {
    Dotenv::createImmutable($envDir, $envFile)->load();
}

$container = new Container();

$getSettings = require __DIR__ . '/../bootstrap/settings.php';
$container->set(SettingsInterface::class, $getSettings);

$container->set(LoggerInterface::class, function (SettingsInterface $settings) {
    $loggerSettings = $settings->get('logger');
    $logger = new Logger($loggerSettings['name']);

    $processor = new UidProcessor();
    $logger->pushProcessor($processor);

    $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
    $logger->pushHandler($handler);

    return $logger;
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

$container->set(Messages::class, function () {
    $storage = [];
    return new Messages($storage);
});

$getPDO = require __DIR__ . '/../bootstrap/database.php';
$container->set(\PDO::class, $getPDO);

return $container;
