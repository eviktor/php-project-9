<?php

use App\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container): \PDO {
    $config = $container->get(SettingsInterface::class)->get('database');
    $pdo = new \PDO(
        "pgsql:"
        . "host={$config['host']};"
        . "port={$config['port']};"
        . "dbname={$config['name']};"
        . "user={$config['user']};"
        . "password={$config['password']}"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
