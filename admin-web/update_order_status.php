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

$order_id = $_POST['order_id'] ?? '';
$new_status = $_POST['status'] ?? '';

if (!$order_id || !$new_status) {
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

$sql = "UPDATE orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $order_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Status updated"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating"]);
}

$stmt->close();
$conn->close();
?>
