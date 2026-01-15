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
    die(json_encode(["success" => false, "message" => "DB connection failed"]));
}

$sql = "SELECT * FROM orders ORDER BY id DESC";
$res = $conn->query($sql);

$orders = [];
while ($row = $res->fetch_assoc()) {
    $oid = $row['id'];

    $it = $conn->prepare("SELECT product_name, price, quantity, image FROM order_items WHERE order_id = ?");
    $it->bind_param("i", $oid);
    $it->execute();
    $itemsRes = $it->get_result();

    $items = [];
    while ($i = $itemsRes->fetch_assoc()) {
        $items[] = $i;
    }

    $row['items'] = $items;
    $orders[] = $row;
}

echo json_encode(["success" => true, "orders" => $orders]);
?>
