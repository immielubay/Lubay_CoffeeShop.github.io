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

$user_id = $_GET['user_id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$orders = [];

while ($row = $res->fetch_assoc()) {
    $order_id = $row['id'];

    $stmtItems = $conn->prepare("SELECT product_name, quantity, price, image FROM order_items WHERE order_id = ?");
    $stmtItems->bind_param("i", $order_id);
    $stmtItems->execute();
    $resItems = $stmtItems->get_result();
    $items = $resItems->fetch_all(MYSQLI_ASSOC);
    $stmtItems->close();

    $orders[] = [
        "id" => $row['id'],  // renamed from order_id
        "order_code" => $row['order_code'],
        "total_amount" => $row['total_amount'],
        "status" => $row['status'],
        "order_date" => $row['order_date'],
        "items" => $items
    ];
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true, "orders" => $orders]);
?>
