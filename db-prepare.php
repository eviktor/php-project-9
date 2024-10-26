<?php

require 'vendor/autoload.php';

$container = require __DIR__ . '/bootstrap/container.php';

$pdo = $container->get(\PDO::class);
$initFilePath = __DIR__ . '/database.sql';
$initSql = file_get_contents($initFilePath);
$pdo->exec($initSql);
