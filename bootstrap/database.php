<?php

use App\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container): \PDO {
    $settings = $container->get(SettingsInterface::class);
    $dsn = $settings->get('databaseDsn');

    $driver = '';
    $pdoDsn = '';
    $dsnScheme = $dsn->getScheme();
    switch ($dsnScheme) {
        case 'pgsql':
        case 'postgres':
        case 'postgresql':
            $driver = "pgsql";
            $params = [
                'host' => $dsn->getHost(),
                'port' => $dsn->getPort(),
                'user' => $dsn->getUser(),
                'password' => $dsn->getPassword(),
                'dbname' => ltrim(urldecode($dsn->getPath()), '/'),
            ];
            $pdoDsn = "$driver:" . http_build_query($params, '', ';');
            break;
        case 'sqlite':
            $driver = "sqlite";
            $pdoDsn = "$driver:" . ltrim(urldecode($dsn->getPath()), '/');
            break;
        default:
            throw new \Exception("Unsupported database scheme $dsnScheme");
    }

    $pdo = new \PDO($pdoDsn);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
