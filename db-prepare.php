<?php

require 'vendor/autoload.php';

$container = require __DIR__ . '/bootstrap/container.php';

$initFilePath = __DIR__ . '/database.pgsql.sql';
$initSql = file_get_contents($initFilePath);
$container->get(\PDO::class)->exec($initSql);
