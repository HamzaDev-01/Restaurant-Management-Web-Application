<?php
require_once 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $conn = getDatabaseConnection();

    // Check if delivery columns exist
    $checkColumns = $conn->query("SHOW COLUMNS FROM orders LIKE 'delivery_status'");
    $hasDeliveryColumns = $checkColumns && $checkColumns->num_rows > 0;

    if (!$hasDeliveryColumns) {
        echo json_encode([
            'success' => false,
            'message' => 'Delivery system not set up. Please run migrate_delivery_columns.php first.',
            'setup_required' => true
        ]);
        exit;
    }

    $orderId = intval($_POST['order_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$orderId || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit;
    }

    $response = ['success' => false, 'message' => ''];

    if ($action === 'confirm') {
        // Rider confirms picking up the order
        $riderName = isset($_POST['rider_name']) ? $conn->real_escape_string($_POST['rider_name']) : '';

        if (empty($riderName)) {
            echo json_encode(['success' => false, 'message' => 'Rider name is required']);
            exit;
        }

        $sql = "UPDATE orders
                SET delivery_status = 'confirmed',
                    rider_name = '$riderName',
                    confirmation_date = NOW()
                WHERE order_id = $orderId AND delivery_status = 'pending'";

        if ($conn->query($sql)) {
            if ($conn->affected_rows > 0) {
                $response = [
                    'success' => true,
                    'message' => 'Order confirmed successfully',
                    'order_id' => $orderId,
                    'rider_name' => $riderName
                ];
            } else {
                // Check if order exists
                $checkOrder = $conn->query("SELECT order_id, delivery_status FROM orders WHERE order_id = $orderId");
                if ($checkOrder && $checkOrder->num_rows > 0) {
                    $orderData = $checkOrder->fetch_assoc();
                    $response = [
                        'success' => false,
                        'message' => "Order is already " . $orderData['delivery_status'] . ". Cannot confirm.",
                        'current_status' => $orderData['delivery_status']
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'Order not found'];
                }
            }
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . $conn->error];
        }
    }
    elseif ($action === 'deliver') {
        // Rider marks order as delivered
        $sql = "UPDATE orders
                SET delivery_status = 'delivered',
                    delivery_date = NOW()
                WHERE order_id = $orderId AND delivery_status = 'confirmed'";

        if ($conn->query($sql)) {
            if ($conn->affected_rows > 0) {
                $response = [
                    'success' => true,
                    'message' => 'Order marked as delivered successfully',
                    'order_id' => $orderId
                ];
            } else {
                // Check if order exists
                $checkOrder = $conn->query("SELECT order_id, delivery_status FROM orders WHERE order_id = $orderId");
                if ($checkOrder && $checkOrder->num_rows > 0) {
                    $orderData = $checkOrder->fetch_assoc();
                    if ($orderData['delivery_status'] === 'pending') {
                        $response = [
                            'success' => false,
                            'message' => 'Order must be confirmed by rider before marking as delivered',
                            'current_status' => $orderData['delivery_status']
                        ];
                    } else if ($orderData['delivery_status'] === 'delivered') {
                        $response = [
                            'success' => false,
                            'message' => 'Order is already delivered',
                            'current_status' => $orderData['delivery_status']
                        ];
                    } else {
                        $response = ['success' => false, 'message' => 'Unable to deliver order'];
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Order not found'];
                }
            }
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . $conn->error];
        }
    }
    else {
        $response = ['success' => false, 'message' => 'Invalid action: ' . $action];
    }

    echo json_encode($response);
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
