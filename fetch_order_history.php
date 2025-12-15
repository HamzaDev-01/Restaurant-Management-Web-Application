<?php
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $conn = getDatabaseConnection();

    // Check if delivery columns exist
    $checkColumns = $conn->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    $hasDeliveryColumns = ($checkColumns && $checkColumns->num_rows > 0);

    // Build query based on column availability
    if ($hasDeliveryColumns) {
        $sql = "SELECT o.order_id, o.customer_name, o.email, o.address, o.phone_number,
                oi.menu_item_name AS item_name, oi.price AS item_price, o.order_date,
                o.delivery_status, o.rider_name, o.confirmation_date, o.delivery_date
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                ORDER BY o.order_date DESC";
    } else {
        // Fallback for old schema without delivery columns
        $sql = "SELECT o.order_id, o.customer_name, o.email, o.address, o.phone_number,
                oi.menu_item_name AS item_name, oi.price AS item_price, o.order_date
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                ORDER BY o.order_date DESC";
    }

    $result = $conn->query($sql);

    $orderHistory = [];

    if ($result && $result->num_rows > 0) {
        // Fetch each row and store in the $orderHistory array
        while ($row = $result->fetch_assoc()) {
            // Add default values for missing columns
            if (!$hasDeliveryColumns) {
                $row['delivery_status'] = 'pending';
                $row['rider_name'] = null;
                $row['confirmation_date'] = null;
                $row['delivery_date'] = null;
            }
            $orderHistory[] = $row;
        }
    }

    echo json_encode($orderHistory);

    $conn->close();

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
