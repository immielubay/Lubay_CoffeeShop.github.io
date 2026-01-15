<?php
// Database connection
$host = "fdb1034.awardspace.net"; 
$user = "4669776_wpress38bf5563";          
$pass = "1234iMMie";     
$dbname = "4669776_wpress38bf5563";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Imgur URLs
$imgurUrls = [
    "Cappuccino"      => "https://i.imgur.com/YOmjA18.png",
    "Mocha"           => "https://i.imgur.com/tCJoQ27.png",
    "Latte"           => "https://i.imgur.com/tUP9FzY.png",
    "Espresso"        => "https://i.imgur.com/WUt528p.png",
    "Caramel Macchiato"=> "https://i.imgur.com/S9rFxze.png",
    "Iced Americano"  => "https://i.imgur.com/HPde9wI.png",
    "Chocolate Frappe"=> "https://i.imgur.com/dq4cgMQ.png",
    "Matcha Latte"    => "https://i.imgur.com/FX9C3qj.png",
    "Hazelnut Latte"  => "https://i.imgur.com/WBKKUXO.png",
    "Spanish Latte"   => "https://i.imgur.com/Kt94iBl.png",
    "Bagel"           => "https://i.imgur.com/w9d5TKM.png",
    "Croissant"       => "https://i.imgur.com/uKJNnD5.png",
    "Chocolate Croissant"=> "https://i.imgur.com/hjPNYdC.png",
    "Cinnamon Roll"   => "https://i.imgur.com/CecIfje.png",
    "Banana Bread"    => "https://i.imgur.com/cCMA4PB.png",
    "Blueberry Muffin"=> "https://i.imgur.com/0QURil1.png",
    "Cheese Ensaymada"=> "https://i.imgur.com/Kj7mNwi.png",
    "Garlic Bread"    => "https://i.imgur.com/e2H9Kad.png",
    "Ham Sandwich"    => "https://i.imgur.com/Qui7Ar1.png"
];

// Update each menu item
foreach ($imgurUrls as $name => $url) {
    $stmt = $conn->prepare("UPDATE menu_items SET image = ? WHERE name = ?");
    $stmt->bind_param("ss", $url, $name);
    $stmt->execute();
    $stmt->close();
}

echo "Menu images updated successfully!";
$conn->close();
?>
