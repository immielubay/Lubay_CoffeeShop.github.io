<?php
include '../include/db_connect.php';

$id = $_POST['id'];

$sql = "DELETE FROM menu_items WHERE id='$id'";

if ($conn->query($sql)) {
    echo "success";
} else {
    echo "error";
}

$conn->close();
?>
