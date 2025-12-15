<?php
// Comprehensive Setup and Testing Script for Delivery System
require_once 'db_config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery System Setup & Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #ff6f61; margin-bottom: 20px; }
        .section { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table th { background: #ff6f61; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #ff6f61; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn:hover { background: #ff5040; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .status-badge { padding: 5px 10px; border-radius: 15px; color: white; font-size: 0.9em; }
        .badge-success { background: #28a745; }
        .badge-error { background: #dc3545; }
        .badge-warning { background: #ffc107; color: #000; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Delivery System Setup & Test</h1>

        <?php
        try {
            $conn = getDatabaseConnection();

            // Step 1: Check Database Connection
            echo '<div class="section">';
            echo '<h2>✅ Step 1: Database Connection</h2>';
            echo '<p class="success">✓ Successfully connected to database: ' . DB_NAME . '</p>';
            echo '</div>';

            // Step 2: Check Tables
            echo '<div class="section">';
            echo '<h2>📋 Step 2: Check Tables</h2>';

            $tables = ['orders', 'order_items', 'menu_items'];
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    echo "<p class='success'>✓ Table '$table' exists</p>";
                } else {
                    echo "<p class='error'>✗ Table '$table' is missing - Run db_init.php first!</p>";
                }
            }
            echo '</div>';

            // Step 3: Check Delivery Columns
            echo '<div class="section">';
            echo '<h2>🚚 Step 3: Check Delivery Columns</h2>';

            $requiredColumns = ['delivery_status', 'rider_name', 'confirmation_date', 'delivery_date'];
            $missingColumns = [];

            foreach ($requiredColumns as $col) {
                $result = $conn->query("SHOW COLUMNS FROM orders LIKE '$col'");
                if ($result && $result->num_rows > 0) {
                    echo "<p class='success'>✓ Column '$col' exists</p>";
                } else {
                    echo "<p class='error'>✗ Column '$col' is missing</p>";
                    $missingColumns[] = $col;
                }
            }

            if (count($missingColumns) > 0) {
                echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">';
                echo '<p class="warning">⚠️ Delivery columns are missing!</p>';
                echo '<p>Please run the migration to add delivery tracking columns:</p>';
                echo '<a href="migrate_delivery_columns.php" class="btn btn-success">Run Migration Now</a>';
                echo '</div>';
            } else {
                echo '<p class="success" style="margin-top: 20px;">✓ All delivery columns are set up correctly!</p>';
            }
            echo '</div>';

            // Step 4: Check Orders
            echo '<div class="section">';
            echo '<h2>📦 Step 4: Orders Status</h2>';

            $ordersResult = $conn->query("SELECT COUNT(*) as total FROM orders");
            $ordersCount = $ordersResult->fetch_assoc()['total'];

            if ($ordersCount > 0) {
                echo "<p class='info'>📊 Total orders in database: <strong>$ordersCount</strong></p>";

                // Show recent orders
                if (count($missingColumns) == 0) {
                    $recentOrders = $conn->query("SELECT order_id, customer_name, total_price, delivery_status, rider_name, order_date
                                                   FROM orders
                                                   ORDER BY order_date DESC
                                                   LIMIT 10");
                } else {
                    $recentOrders = $conn->query("SELECT order_id, customer_name, total_price, order_date
                                                   FROM orders
                                                   ORDER BY order_date DESC
                                                   LIMIT 10");
                }

                if ($recentOrders && $recentOrders->num_rows > 0) {
                    echo '<h3>Recent Orders:</h3>';
                    echo '<table>';
                    echo '<tr><th>Order ID</th><th>Customer</th><th>Total</th>';
                    if (count($missingColumns) == 0) {
                        echo '<th>Status</th><th>Rider</th>';
                    }
                    echo '<th>Date</th><th>Actions</th></tr>';

                    while ($order = $recentOrders->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $order['order_id'] . '</td>';
                        echo '<td>' . htmlspecialchars($order['customer_name']) . '</td>';
                        echo '<td>$' . number_format($order['total_price'], 2) . '</td>';

                        if (count($missingColumns) == 0) {
                            $status = $order['delivery_status'];
                            $badgeClass = $status === 'delivered' ? 'badge-success' : ($status === 'confirmed' ? 'badge-warning' : 'badge-error');
                            echo '<td><span class="status-badge ' . $badgeClass . '">' . ucfirst($status) . '</span></td>';
                            echo '<td>' . ($order['rider_name'] ? htmlspecialchars($order['rider_name']) : '-') . '</td>';
                        }

                        echo '<td>' . date('Y-m-d H:i', strtotime($order['order_date'])) . '</td>';
                        echo '<td><a href="track_order.php?order_id=' . $order['order_id'] . '" target="_blank" class="btn" style="padding: 5px 10px; font-size: 0.9em;">Track</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } else {
                echo '<p class="warning">⚠️ No orders in database. Place an order to test the system.</p>';
                echo '<a href="index.html" class="btn">Go to Homepage</a>';
            }
            echo '</div>';

            // Step 5: Test APIs
            echo '<div class="section">';
            echo '<h2>🔧 Step 5: Test API Endpoints</h2>';

            $apis = [
                'fetch_order_history.php' => 'Fetch Order History',
                'get_order_status.php?order_id=1' => 'Get Order Status',
                'fetch_orders_for_rider.php' => 'Fetch Orders for Rider'
            ];

            foreach ($apis as $file => $description) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/' . $file;
                echo "<p><strong>$description:</strong> <a href='$url' target='_blank'>$url</a></p>";
            }
            echo '</div>';

            // Step 6: Quick Actions
            echo '<div class="section">';
            echo '<h2>⚡ Quick Actions</h2>';
            echo '<a href="index.html" class="btn">🏠 Homepage</a>';
            echo '<a href="rider_dashboard.php" class="btn">🛵 Rider Dashboard</a>';
            echo '<a href="track_order.php" class="btn">🔍 Track Order</a>';
            echo '<a href="dashboard.php" class="btn">👨‍💼 Admin Dashboard</a>';
            echo '<a href="check_db_structure.php" class="btn">🗄️ Check DB Structure</a>';

            if (count($missingColumns) > 0) {
                echo '<a href="migrate_delivery_columns.php" class="btn btn-success">▶️ Run Migration</a>';
            }
            echo '</div>';

            // Step 7: System Info
            echo '<div class="section">';
            echo '<h2>ℹ️ System Information</h2>';
            echo '<table>';
            echo '<tr><th>Item</th><th>Value</th></tr>';
            echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
            echo '<tr><td>Database Host</td><td>' . DB_HOST . '</td></tr>';
            echo '<tr><td>Database Name</td><td>' . DB_NAME . '</td></tr>';
            echo '<tr><td>Server Software</td><td>' . $_SERVER['SERVER_SOFTWARE'] . '</td></tr>';
            echo '<tr><td>Document Root</td><td>' . $_SERVER['DOCUMENT_ROOT'] . '</td></tr>';
            echo '</table>';
            echo '</div>';

            // Step 8: Troubleshooting
            if (count($missingColumns) > 0 || $ordersCount == 0) {
                echo '<div class="section" style="background: #fff3cd; border-left: 5px solid #ffc107;">';
                echo '<h2>🔧 Troubleshooting Steps</h2>';

                if (count($missingColumns) > 0) {
                    echo '<h3>Missing Delivery Columns:</h3>';
                    echo '<ol>';
                    echo '<li>Click the <strong>"Run Migration"</strong> button above</li>';
                    echo '<li>Wait for the migration to complete</li>';
                    echo '<li>Refresh this page to verify</li>';
                    echo '</ol>';
                }

                if ($ordersCount == 0) {
                    echo '<h3>No Orders to Test:</h3>';
                    echo '<ol>';
                    echo '<li>Go to the <a href="index.html">Homepage</a></li>';
                    echo '<li>Click "Order" in the sidebar</li>';
                    echo '<li>Add items to cart and place an order</li>';
                    echo '<li>Note the Order ID</li>';
                    echo '<li>Use the Order ID to test tracking</li>';
                    echo '</ol>';
                }

                echo '</div>';
            }

            $conn->close();

        } catch (Exception $e) {
            echo '<div class="section" style="background: #f8d7da; border-left: 5px solid #dc3545;">';
            echo '<h2 class="error">❌ Error</h2>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>

        <div class="section" style="background: #e7f3ff; border-left: 5px solid #17a2b8;">
            <h2>📚 Documentation</h2>
            <ul>
                <li><a href="SETUP_DELIVERY_SYSTEM.md" target="_blank">Delivery System Setup Guide</a></li>
                <li><a href="ORDER_TRACKING_FEATURES.md" target="_blank">Order Tracking Features</a></li>
                <li><a href="SETUP_INSTRUCTIONS.md" target="_blank">General Setup Instructions</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
