<?php
include __DIR__ . '/include/db_connect.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $res = $conn->query("DELETE FROM admin_todo WHERE id=$id");
    if (!$res) die("Delete failed: " . $conn->error);
}

header("Location: index.php");
exit;
