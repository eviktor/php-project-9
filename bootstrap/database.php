<?php

use App\Settings\SettingsInterface;
use Psr\Container\ContainerInterface;

return function (ContainerInterface $container): \PDO {
    $settings = $container->get(SettingsInterface::class);
    $dsn = $settings->get('databaseDsn');

    $driver = '';
    $pdoDsn = '';
    $initFileName = 'database.sql';
    switch ($dsn->getScheme()) {
        case 'pgsql':
        case 'postgresql':
            $driver = "pgsql";
            $params = [
                'host' => $dsn->getHost(),
                'port' => $dsn->getPort(),
                'user' => $dsn->getUser(),
                'password' => $dsn->getPassword(),
                'dbname' => ltrim(urldecode($dsn->getPath()), '/'),
            ];
            $pdoDsn = "$driver:"
                . http_build_query($params, '', ';');
            break;
        case 'sqlite':
            $driver = "sqlite";
            $pdoDsn = "$driver:" . ltrim(urldecode($dsn->getPath()), '/');
            $initFileName = 'database.sqlite.sql';
            break;
        default:
            throw new \Exception('Unsupported database scheme');
    }

    $pdo = new \PDO($pdoDsn);

    if ($settings->get('env') === 'local' || $settings->get('env') === 'testing') {
        $initFilePath = __DIR__ . "/../$initFileName";
        $initSql = file_get_contents($initFilePath);
        $pdo->exec($initSql);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};
