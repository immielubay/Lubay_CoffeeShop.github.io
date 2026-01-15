<?php
$host = "fdb1034.awardspace.net"; 
$user = "4669776_wpress38bf5563";          
$pass = "1234iMMie";     
$dbname = "4669776_wpress38bf5563";       

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}
?>