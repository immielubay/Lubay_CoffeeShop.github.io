<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "fdb1034.awardspace.net";
$username = "4669776_wpress38bf5563";
$password = "1234iMMie";
$dbname = "4669776_wpress38bf5563";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed."]));
}

$user_id = $_POST['user_id'] ?? '';
$item_name = $_POST['item_name'] ?? '';
$item_price = $_POST['item_price'] ?? '';
$item_image = $_POST['item_image'] ?? '';

if (!$user_id || !$item_name || !$item_price) {
    echo json_encode(["success" => false, "message" => "Missing data."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO cart (user_id, item_name, item_price, item_image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isds", $user_id, $item_name, $item_price, $item_image);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item added to cart!"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add to cart."]);
}
$stmt->close();
$conn->close();
?>
