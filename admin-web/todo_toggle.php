<?php
include __DIR__ . '/include/db_connect.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $res = $conn->query("UPDATE admin_todo SET is_done = 1 - is_done WHERE id=$id");
    if (!$res) die("Update failed: " . $conn->error);
}

header("Location: index.php");
exit;
