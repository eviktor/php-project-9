<?php

require 'vendor/autoload.php';

$container = require __DIR__ . '/bootstrap/container.php';

$pdo = $container->get(\PDO::class);
$initFilePath = __DIR__ . '/database.pgsql.sql';
$initSql = file_get_contents($initFilePath);
$pdo->exec('DROP TABLE IF EXISTS urls, url_checks;');
$pdo->exec($initSql);
