<?php
require_once 'db_config.php';

$conn = getDatabaseConnection();

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

        // Return success with order tracking information
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $orderId,
            'track_url' => "track_order.php?order_id=$orderId"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No cart items were submitted.']);
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
