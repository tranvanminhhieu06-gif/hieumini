<?php
require_once __DIR__ . '/config/db.php';
$sql = file_get_contents(__DIR__ . '/database.sql');
$pdo->exec($sql);
echo "Database re-seeded with 100% UTF-8 encoding successfully!\n";
?>