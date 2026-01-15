<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "fdb1034.awardspace.net";
$username = "4669776_wpress38bf5563";
$password = "1234iMMie";
$dbname = "4669776_wpress38bf5563";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

$item_id = $_POST['item_id'] ?? '';

if (empty($item_id)) {
    echo json_encode(["success" => false, "message" => "Missing item_id."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item removed from cart."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove item."]);
}

$stmt->close();
$conn->close();
?>
