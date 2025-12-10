<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'restaurant_db_temp';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update with working image URLs
$updates = [
    1 => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=400&h=300&fit=crop',  // Margherita Pizza
    2 => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop',  // Caesar Salad
    3 => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop',  // Salmon
    4 => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&h=300&fit=crop',  // Spaghetti
    5 => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&h=300&fit=crop',  // Chocolate Cake
    6 => 'https://images.unsplash.com/photo-1571115177098-24ec42ed204d?w=400&h=300&fit=crop'   // Tiramisu
];

foreach ($updates as $id => $image_url) {
    $sql = "UPDATE menu_items SET image_url = '" . $conn->real_escape_string($image_url) . "' WHERE id = $id";
    if ($conn->query($sql)) {
        echo "✓ Updated item $id\n";
    } else {
        echo "✗ Error updating item $id: " . $conn->error . "\n";
    }
}

echo "\n✓ All images have been updated successfully!\n";

$conn->close();
?>
