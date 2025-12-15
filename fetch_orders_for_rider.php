<?php
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $conn = getDatabaseConnection();

    // Check if delivery columns exist
    $checkColumns = $conn->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    $hasDeliveryColumns = $checkColumns && $checkColumns->num_rows > 0;

    // Query to fetch all orders with their items
    if ($hasDeliveryColumns) {
        $sql = "SELECT
            o.order_id,
            o.customer_name,
            o.email,
            o.address,
            o.phone_number,
            o.total_price,
            o.order_date,
            o.delivery_status,
            o.rider_name,
            o.confirmation_date,
            o.delivery_date,
            oi.menu_item_name,
            oi.price AS item_price,
            oi.quantity
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        ORDER BY
            CASE o.delivery_status
                WHEN 'pending' THEN 1
                WHEN 'confirmed' THEN 2
                WHEN 'delivered' THEN 3
            END,
            o.order_date DESC";
    } else {
        $sql = "SELECT
            o.order_id,
            o.customer_name,
            o.email,
            o.address,
            o.phone_number,
            o.total_price,
            o.order_date,
            oi.menu_item_name,
            oi.price AS item_price,
            oi.quantity
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        ORDER BY o.order_date DESC";
    }

    $result = $conn->query($sql);

    $orders = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Add default values for missing columns
            if (!$hasDeliveryColumns) {
                $row['delivery_status'] = 'pending';
                $row['rider_name'] = null;
                $row['confirmation_date'] = null;
                $row['delivery_date'] = null;
            }
            $orders[] = $row;
        }
    }

    echo json_encode($orders);

    $conn->close();

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
