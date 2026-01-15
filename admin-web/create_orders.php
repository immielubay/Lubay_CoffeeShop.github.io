<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

ini_set("display_errors", 1);
error_reporting(E_ALL);

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

$input = json_decode(file_get_contents("php://input"), true);
if ($input) { $_POST = array_merge($_POST, $input); }

$user_id      = $_POST["user_id"] ?? "";
$total_amount = $_POST["total_amount"] ?? "";

// BUY NOW ONLY FIELDS
$single_name  = $_POST["product_name"] ?? null;
$single_qty   = $_POST["quantity"] ?? null;
$single_price = $_POST["price"] ?? null;
$single_img   = $_POST["image"] ?? null;

// Validate basic fields
if (!$user_id || !is_numeric($total_amount)) {
    echo json_encode(["success" => false, "message" => "Missing user_id or total_amount"]);
    exit;
}

$conn->begin_transaction();

try {

    // 1. Create order header
    $order_code = "ORD" . rand(100000, 999999);
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, order_code, total_amount, status, order_date)
        VALUES (?, ?, ?, 'unpaid', NOW())
    ");
    
    $total_str = number_format((float)$total_amount, 2, ".", "");
    $stmt->bind_param("iss", $user_id, $order_code, $total_str);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // 2. INSERT ORDER ITEMS
    $ins = $conn->prepare("
        INSERT INTO order_items (order_id, product_name, price, quantity, image)
        VALUES (?, ?, ?, ?, ?)
    ");

    // Case A: BUY-NOW (only 1 item)
    if ($single_name !== null && $single_price !== null) {
        $price_str = number_format((float)$single_price, 2, ".", "");
        $qty = intval($single_qty ?? 1);

        $ins->bind_param("issis", $order_id, $single_name, $price_str, $qty, $single_img);
        $ins->execute();
    }

    // Case B: CHECKOUT FROM CART
    else {
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $cartItems = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($cartItems as $row) {
            $price_str = number_format((float)$row["item_price"], 2, ".", "");
            $qty = intval($row["quantity"] ?? 1);

            $ins->bind_param("issis", 
                $order_id,
                $row["item_name"],
                $price_str,
                $qty,
                $row["item_image"]
            );
            $ins->execute();
        }

        // Remove items from cart
        $del = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $del->bind_param("i", $user_id);
        $del->execute();
        $del->close();
    }

    $ins->close();
    $conn->commit();

    echo json_encode([
        "success" => true,
        "message" => "Order created successfully",
        "order_id" => $order_id,
        "order_code" => $order_code
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Checkout failed: " . $e->getMessage()]);
}

$conn->close();
?>
