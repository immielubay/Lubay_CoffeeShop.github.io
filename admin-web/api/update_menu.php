<?php
include '../include/db_connect.php';

$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$category = $_POST['category'];
$image = $_POST['image'];

$sql = "UPDATE menu_items SET 
        name='$name',
        description='$description',
        price='$price',
        category='$category',
        image='$image'
        WHERE id='$id'";

if ($conn->query($sql)) {
    echo "success";
} else {
    echo "error";
}
?>
