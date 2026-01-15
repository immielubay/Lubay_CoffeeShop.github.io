<?php
include '../include/db_connect.php';

$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];
$image = $_POST['image']; // path only

$sql = "INSERT INTO menu_items (name, description, price, category, image)
        VALUES ('$name', '$description', '$price', '$category', '$image')";

if ($conn->query($sql)) {
    echo "success";
} else {
    echo "error";
}

$conn->close();
?>
