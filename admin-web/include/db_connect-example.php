<?php
// Database connection settings
$host = "your_host_here";
$username = "your_username_here";
$password = "your_password_here";
$database = "your_database_here";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
