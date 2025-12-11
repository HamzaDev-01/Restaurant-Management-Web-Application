<?php

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "restaurant_db_temp"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $conn->real_escape_string($_POST['customerName']);
    $customerEmail = $conn->real_escape_string($_POST['customerEmail']);
    $customerAddress = $conn->real_escape_string($_POST['customerAddress']);
    $customerPhone = $conn->real_escape_string($_POST['customerPhone']);
    $totalPrice = 0;

    if (isset($_POST['cart']) && is_array($_POST['cart'])) {
        $cart = $_POST['cart'];
        foreach ($cart as $item) {
            $itemName = $conn->real_escape_string($item['name']);
            $price = floatval($item['price']);
            $quantity = intval($item['quantity']);
            $totalPrice += $price * $quantity;
        }

        // Insert order into `orders` table
        $conn->query("INSERT INTO orders (customer_name, email, address, phone_number, total_price) VALUES ('$customerName', '$customerEmail', '$customerAddress', '$customerPhone', $totalPrice)");
        $orderId = $conn->insert_id;

        // Insert items into `order_items` table
        foreach ($cart as $item) {
            $itemName = $item['name'];
            $price = floatval($item['price']);
            $quantity = intval($item['quantity']);
            $conn->query("INSERT INTO order_items (order_id, menu_item_name, price, quantity) VALUES ($orderId, '$itemName', $price, $quantity)");
        }

        echo "Order placed successfully! Order ID: $orderId";
    } else {
        echo "No cart items were submitted.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
