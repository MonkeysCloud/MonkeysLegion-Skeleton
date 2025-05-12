<?php
require __DIR__.'/vendor/autoload.php';

$conn = (require __DIR__.'/../bootstrap.php')->getContainer()
    ->get(MonkeysLegion\Database\MySQL\Connection::class);

$rows = $conn->pdo()->query('SELECT VERSION()')->fetchAll();
print_r($rows);