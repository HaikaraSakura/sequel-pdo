<?php

declare(strict_types=1);

use Haikara\SequelPdo\SequelPDO;
use Haikara\SequelPdo\SequelPDOInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = new PDO(
    'mysql:host=mysql;dbname=test_db',
    'adminer',
    'adminer',
    SequelPDOInterface::PDO_MYSQL_OPTIONS
);

$db = new SequelPDO($pdo);

print_r($db);

print_r($db->exeQuery('select * from items')->fetchAll());
