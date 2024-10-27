<?php

use App\Settings\SettingsInterface;

require 'vendor/autoload.php';

$container = require __DIR__ . '/bootstrap/container.php';

$settings = $container->get(SettingsInterface::class);
$pdo = $container->get(\PDO::class);

$scheme = $settings->get('databaseDsn')->getScheme();
$initFilePath = __DIR__ . ($scheme === 'sqlite' ? '/database.sqlite.sql' : '/database.sql');
$initSql = file_get_contents($initFilePath);
$pdo->exec($initSql);
