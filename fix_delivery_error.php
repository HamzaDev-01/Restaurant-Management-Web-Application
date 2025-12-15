<?php
// ONE-CLICK FIX for "Delivery system not set up" error
// Just run this file once and everything will be fixed!

require_once 'db_config.php';
header('Content-Type: text/html; charset=utf-8');

$conn = getDatabaseConnection();
$success = true;
$messages = [];

// Add all missing columns
$columns = [
    'delivery_status' => "ALTER TABLE orders ADD COLUMN delivery_status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending'",
    'rider_name' => "ALTER TABLE orders ADD COLUMN rider_name VARCHAR(100)",
    'confirmation_date' => "ALTER TABLE orders ADD COLUMN confirmation_date TIMESTAMP NULL",
    'delivery_date' => "ALTER TABLE orders ADD COLUMN delivery_date TIMESTAMP NULL"
];

foreach ($columns as $columnName => $sql) {
    // Check if column exists
    $check = $conn->query("SHOW COLUMNS FROM orders LIKE '$columnName'");

    if ($check && $check->num_rows == 0) {
        // Column doesn't exist, add it
        if ($conn->query($sql)) {
            $messages[] = "✓ Added column: $columnName";
        } else {
            $messages[] = "✗ Failed to add column: $columnName - " . $conn->error;
            $success = false;
        }
    } else {
        $messages[] = "✓ Column already exists: $columnName";
    }
}

// Update existing orders
if ($success) {
    $conn->query("UPDATE orders SET delivery_status = 'pending' WHERE delivery_status IS NULL");
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Complete!</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 15px; padding: 40px; max-width: 600px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); text-align: center; }
        h1 { color: #28a745; margin-bottom: 20px; font-size: 2.5em; }
        .icon { font-size: 5em; margin: 20px 0; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; text-align: left; }
        .error-msg { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 15px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 10px; font-weight: bold; font-size: 1.1em; }
        .btn:hover { background: #218838; }
        .btn-secondary { background: #667eea; }
        .btn-secondary:hover { background: #5568d3; }
        ul { text-align: left; margin: 20px 0; }
        ul li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <div class="icon">🎉</div>
            <h1>All Fixed!</h1>
            <p style="font-size: 1.2em; margin: 20px 0; color: #28a745;">
                Your delivery system is now ready to use!
            </p>

            <div style="text-align: left; margin: 20px 0;">
                <h3>What was fixed:</h3>
                <ul>
                    <?php foreach ($messages as $msg): ?>
                        <li><?php echo htmlspecialchars($msg); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="margin-top: 30px;">
                <a href="index.html" class="btn">🏠 Go to Homepage</a>
                <a href="rider_dashboard.php" class="btn btn-secondary">🛵 Rider Dashboard</a>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
                <h4>Try it now:</h4>
                <ol style="text-align: left;">
                    <li>Place an order from the homepage</li>
                    <li>Go to Rider Dashboard</li>
                    <li>Confirm the order</li>
                    <li>Mark it as delivered</li>
                </ol>
            </div>
        <?php else: ?>
            <div class="icon">⚠️</div>
            <h1>Setup Had Issues</h1>

            <div style="text-align: left; margin: 20px 0;">
                <h3>Messages:</h3>
                <ul>
                    <?php foreach ($messages as $msg): ?>
                        <li class="<?php echo strpos($msg, '✗') !== false ? 'error-msg' : 'message'; ?>">
                            <?php echo htmlspecialchars($msg); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="margin-top: 30px;">
                <a href="auto_setup_delivery.php" class="btn">Try Auto Setup</a>
                <a href="setup_and_test.php" class="btn btn-secondary">Diagnostic Tool</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
