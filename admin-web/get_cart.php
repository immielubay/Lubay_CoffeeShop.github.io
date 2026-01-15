<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "fdb1034.awardspace.net";
$username = "4669776_wpress38bf5563";
$password = "1234iMMie";
$dbname = "4669776_wpress38bf5563";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

$user_id = $_POST['user_id'] ?? ''; 

if (empty($user_id)) {
    echo json_encode(["success" => false, "message" => "Missing user_id."]);
    exit;
}

$sql = "SELECT id, item_name, item_price, item_image FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cart[] = [
            "id" => $row["id"],
            "item_name" => $row["item_name"],
            "item_price" => $row["item_price"],
            "item_image" => $row["item_image"]
        ];
    }

    echo json_encode(["success" => true, "cart" => $cart]);
} else {
    echo json_encode(["success" => false, "message" => "Your cart is empty."]);
}

$stmt->close();
$conn->close();
?>
