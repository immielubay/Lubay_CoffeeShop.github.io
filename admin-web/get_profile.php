<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "fdb1034.awardspace.net";
$username   = "4669776_wpress38bf5563";
$password   = "1234iMMie";
$dbname     = "4669776_wpress38bf5563";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false]));
}

$email = $_GET['email'] ?? '';

$stmt = $conn->prepare("SELECT name, email, address, profile_image FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);

$stmt->close();
$conn->close();
?>
