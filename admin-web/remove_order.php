<?php
session_start();
include 'include/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit;
}

$order_id = intval($_POST['order_id'] ?? 0);

if($order_id > 0){
    // delete order items first
    $conn->query("DELETE FROM order_items WHERE order_id=$order_id");
    // delete order
    if($conn->query("DELETE FROM orders WHERE id=$order_id")){
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'message'=>$conn->error]);
    }
} else {
    echo json_encode(['success'=>false,'message'=>'Invalid order ID']);
}
