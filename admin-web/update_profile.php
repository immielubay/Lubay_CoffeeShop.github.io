<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

$servername = "fdb1034.awardspace.net"; 
$username   = "4669776_wpress38bf5563";
$password   = "1234iMMie";
$dbname     = "4669776_wpress38bf5563"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

$original_email = $_POST['original_email'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';

if (empty($original_email)) {
    echo json_encode(["success" => false, "message" => "Missing user email."]);
    exit;
}

// Handle profile image upload
$profileImagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = uniqid() . "_" . basename($_FILES['profile_image']['name']);
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        $profileImagePath = $targetFile;
    }
}

// Update query
if ($profileImagePath) {
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, address=?, profile_image=? WHERE email=?");
    $stmt->bind_param("sssss", $name, $email, $address, $profileImagePath, $original_email);
} else {
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, address=? WHERE email=?");
    $stmt->bind_param("ssss", $name, $email, $address, $original_email);
}

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Profile updated successfully.",
        "profile_image" => $profileImagePath
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update profile."]);
}


$stmt->close();
$conn->close();
?>
