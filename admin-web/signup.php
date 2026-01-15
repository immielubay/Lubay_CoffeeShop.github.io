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
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Get form data
$name     = $_POST['name'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$address  = $_POST['address'] ?? '';

// Check required fields
if (empty($name) || empty($email) || empty($password) || empty($address)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered."]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Handle profile image upload
$profileImagePath = "uploads/default.png"; // default profile picture
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = uniqid() . "_" . basename($_FILES['profile_image']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        $profileImagePath = $targetFile;
    } else {
        echo json_encode(["success" => false, "message" => "Failed to upload profile image."]);
        exit;
    }
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (name, email, password, address, profile_image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $hashedPassword, $address, $profileImagePath);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Signup successful!"]);
} else {
    echo json_encode(["success" => false, "message" => "Signup failed."]);
}

$stmt->close();
$conn->close();
?>
