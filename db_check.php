<?php
require_once __DIR__ . '/database/database.php';
echo "<pre>";
echo "ENV:\n";
$keys = ['DB_HOST','DB_USER','DB_NAME','DB_PASS','DB_PORT'];
foreach ($keys as $k) {
  $v = getenv($k);
  if ($v === false) $v = '(not set)';
  if ($k === 'DB_PASS' && $v !== '(not set)') $v = '***';
  echo "$k=$v\n";
}
try {
  $db = new Database();
  $pdo = $db->conn;
  $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
  echo "\nConnected database: " . ($dbName ?: '(none)') . "\n";
  $tables = [];
  try { $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN); } catch (Exception $e) {}
  echo "Tables: " . (count($tables) ? implode(', ', $tables) : '(none)') . "\n";
  foreach (['faculty_details','student_details','course_details'] as $t) {
    try { $cnt = (int)$pdo->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn(); } catch (Exception $e) { $cnt = 'n/a'; }
    echo "$t: $cnt\n";
  }
} catch (Exception $e) {
  echo "Connection error: " . $e->getMessage() . "\n";
}
echo "</pre>";

?>
