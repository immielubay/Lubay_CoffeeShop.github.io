<?php
include '../include/db_connect.php';

$sql = "SELECT * FROM menu_items ORDER BY id DESC";
$result = $conn->query($sql);

$menu = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu[] = $row;
    }
}

echo json_encode($menu);
?>
