<?php
require_once 'db_config.php';

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$orderId = intval($_GET['order_id']);

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

try {
    $conn = getDatabaseConnection();

    // Check if delivery columns exist
    $checkColumns = $conn->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    $hasDeliveryColumns = $checkColumns && $checkColumns->num_rows > 0;

    // Fetch order details
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
            o.delivery_date
        FROM orders o
        WHERE o.order_id = $orderId";
    } else {
        $sql = "SELECT
            o.order_id,
            o.customer_name,
            o.email,
            o.address,
            o.phone_number,
            o.total_price,
            o.order_date
        FROM orders o
        WHERE o.order_id = $orderId";
    }

    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        $conn->close();
        exit;
    }

    $order = $result->fetch_assoc();

    // Add default values for missing columns
    if (!$hasDeliveryColumns) {
        $order['delivery_status'] = 'pending';
        $order['rider_name'] = null;
        $order['confirmation_date'] = null;
        $order['delivery_date'] = null;
    }

    // Fetch order items
    $itemsSql = "SELECT menu_item_name, price, quantity
                 FROM order_items
                 WHERE order_id = $orderId";

    $itemsResult = $conn->query($itemsSql);
    $items = [];

    if ($itemsResult) {
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
    }

    $order['items'] = $items;

    echo json_encode([
        'success' => true,
        'order' => $order
    ]);

    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
