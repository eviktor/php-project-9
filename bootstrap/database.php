<?php

use App\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container): \PDO {
    $config = $container->get(SettingsInterface::class)->get('database');

    $params = [
        'host' => $config['host'],
        'port' => $config['port'],
        'user' => $config['user'],
        'password' => $config['password'],
        'dbname' => $config['name'],
    ];
    $dsn =
        ($config['driver'] === 'postgresql' ? 'pgsql:' : "{$config['driver']}:")
        . http_build_query($params, '', ';');

    $pdo = new \PDO($dsn);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
