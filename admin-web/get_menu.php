<?php
header("Access-Control-Allow-Origin: *"); // allow all origins
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
$servername = "fdb1034.awardspace.net";
$username   = "4669776_wpress38bf5563";
$password   = "1234iMMie";
$dbname     = "4669776_wpress38bf5563";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

// Imgur URLs (direct links)
$imgurUrls = [
    "Cappuccino"       => "https://i.imgur.com/YOmjA18.png",
    "Mocha"            => "https://i.imgur.com/tCJoQ27.png",
    "Latte"            => "https://i.imgur.com/tUP9FzY.png",
    "Espresso"         => "https://i.imgur.com/WUt528p.png",
    "Caramel Macchiato" => "https://i.imgur.com/S9rFxze.png",
    "Iced Americano"   => "https://i.imgur.com/HPde9wI.png",
    "Chocolate Frappe" => "https://i.imgur.com/dq4cgMQ.png",
    "Matcha Latte"     => "https://i.imgur.com/FX9C3qj.png",
    "Hazelnut Latte"   => "https://i.imgur.com/WBKKUXO.png",
    "Spanish Latte"    => "https://i.imgur.com/Kt94iBl.png",
    "Bagel"            => "https://i.imgur.com/w9d5TKM.png",
    "Croissant"        => "https://i.imgur.com/uKJNnD5.png",
    "Chocolate Croissant" => "https://i.imgur.com/hjPNYdC.png",
    "Cinnamon Roll"    => "https://i.imgur.com/CecIfje.png",
    "Banana Bread"     => "https://i.imgur.com/cCMA4PB.png",
    "Blueberry Muffin" => "https://i.imgur.com/0QURil1.png",
    "Cheese Ensaymada" => "https://i.imgur.com/Kj7mNwi.png",
    "Garlic Bread"     => "https://i.imgur.com/e2H9Kad.png",
    "Ham Sandwich"     => "https://i.imgur.com/Qui7Ar1.png"
];

// Get category from URL
$category = isset($_GET["category"]) ? $_GET["category"] : "";

// Build query
if ($category === "") {
    $sql = "SELECT * FROM menu_items";
} else {
    $category = $conn->real_escape_string($category);
    $sql = "SELECT * FROM menu_items WHERE category = '$category'";
}

$result = $conn->query($sql);

$menu = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Use Imgur URL if available, otherwise fallback to empty string
        $imagePath = isset($imgurUrls[$row["name"]]) ? $imgurUrls[$row["name"]] : "";

        $menu[] = [
            "id"          => $row["id"],
            "name"        => $row["name"],
            "description" => $row["description"],
            "price"       => $row["price"],
            "category"    => $row["category"],
            "image"       => $imagePath
        ];
    }
}

// Return JSON
echo json_encode($menu);

$conn->close();
?>
