<?php

declare(strict_types=1);

namespace App\Database;

use App\Settings\SettingsInterface;

final class Connection
{
    private string $dsn;

    public function __construct(SettingsInterface $settings)
    {
        $config = $settings->get('database');
        $params = [
            'host' => $config['host'],
            'port' => $config['port'],
            'dbname' => $config['name'],
            'user' => $config['user'],
            'password' => $config['password'],
        ];
        $this->dsn = "pgsql:" . http_build_query($params, '', ';');
    }

    public function connect(): \PDO
    {
        $pdo = new \PDO($this->dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
