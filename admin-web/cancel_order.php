<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli(
    "fdb1034.awardspace.net",
    "4669776_wpress38bf5563",
    "1234iMMie",
    "4669776_wpress38bf5563"
);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "DB connection failed"]);
    exit;
}

$order_id = $_POST["order_id"] ?? "";

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Missing order_id"]);
    exit;
}


$stmt = $conn->prepare("UPDATE orders SET status='cancelled' WHERE id=? AND status='unpaid'");
$stmt->bind_param("i", $order_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Order cancelled"]);
} else {
    echo json_encode(["success" => false, "message" => "Cannot cancel this order"]);
}

$stmt->close();
$conn->close();
?>
