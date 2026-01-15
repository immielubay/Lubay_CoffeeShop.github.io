<?php
include __DIR__ . '/include/db_connect.php';

$task = $_POST['task'] ?? '';

if ($task) {
    $stmt = $conn->prepare("INSERT INTO admin_todo (task) VALUES (?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    
    $stmt->bind_param("s", $task);
    if (!$stmt->execute()) die("Execute failed: " . $stmt->error);
    
    $stmt->close();
}

header("Location: index.php");
exit;
