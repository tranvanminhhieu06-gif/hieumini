import re

# Read database.sql with utf-8 encoding
with open(r"c:\Users\tranv\Desktop\HieuWeb03\database.sql", "r", encoding="utf-8") as f:
    sql_content = f.read()

# We can execute the SQL commands via PHP script to ensure 100% native PDO UTF-8 insertion
php_runner = """<?php
require_once __DIR__ . '/config/db.php';
$sql = file_get_contents(__DIR__ . '/database.sql');
$pdo->exec($sql);
echo "Database re-seeded with 100% UTF-8 encoding successfully!\\n";
?>"""

with open(r"c:\Users\tranv\Desktop\HieuWeb03\reseed.php", "w", encoding="utf-8") as f:
    f.write(php_runner)

print("Created reseed.php script.")
