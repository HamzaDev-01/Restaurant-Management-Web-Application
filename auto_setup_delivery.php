<?php
// Automatic Delivery System Setup - Run this once to fix the error
require_once 'db_config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Setup Delivery System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 15px; padding: 40px; max-width: 800px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        h1 { color: #667eea; margin-bottom: 20px; text-align: center; }
        .success { color: #28a745; font-weight: bold; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; font-weight: bold; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #17a2b8; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; padding: 10px; background: #fff3cd; border-radius: 5px; margin: 10px 0; }
        .step { margin: 20px 0; padding: 15px; background: #f8f9fa; border-left: 4px solid #667eea; border-radius: 5px; }
        .btn { display: inline-block; padding: 12px 25px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; font-weight: bold; }
        .btn:hover { background: #5568d3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; font-size: 0.9em; }
        .progress { margin: 20px 0; }
        .progress-bar { height: 30px; background: #e9ecef; border-radius: 15px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Automatic Delivery System Setup</h1>

        <?php
        try {
            $conn = getDatabaseConnection();
            $totalSteps = 5;
            $currentStep = 0;
            $errors = [];
            $warnings = [];

            echo '<div class="progress">';
            echo '<div class="progress-bar"><div class="progress-fill" id="progress" style="width: 0%;">0%</div></div>';
            echo '</div>';

            // Step 1: Check database connection
            echo '<div class="step">';
            echo '<h3>Step 1: Checking Database Connection</h3>';
            if ($conn) {
                echo '<p class="success">✓ Connected to database: ' . DB_NAME . '</p>';
                $currentStep++;
            } else {
                echo '<p class="error">✗ Failed to connect to database</p>';
                throw new Exception('Database connection failed');
            }
            echo '</div>';
            echo '<script>document.getElementById("progress").style.width = "' . ($currentStep/$totalSteps*100) . '%"; document.getElementById("progress").textContent = "' . ($currentStep/$totalSteps*100) . '%";</script>';

            // Step 2: Check if orders table exists
            echo '<div class="step">';
            echo '<h3>Step 2: Checking Orders Table</h3>';
            $result = $conn->query("SHOW TABLES LIKE 'orders'");
            if ($result && $result->num_rows > 0) {
                echo '<p class="success">✓ Orders table exists</p>';
                $currentStep++;
            } else {
                echo '<p class="error">✗ Orders table not found. Please run db_init.php first!</p>';
                echo '<a href="db_init.php" class="btn btn-success">Run Database Setup</a>';
                throw new Exception('Orders table not found');
            }
            echo '</div>';
            echo '<script>document.getElementById("progress").style.width = "' . ($currentStep/$totalSteps*100) . '%"; document.getElementById("progress").textContent = "' . round($currentStep/$totalSteps*100) . '%";</script>';

            // Step 3: Check for delivery columns
            echo '<div class="step">';
            echo '<h3>Step 3: Checking Delivery Columns</h3>';

            $deliveryColumns = [
                'delivery_status' => "ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending'",
                'rider_name' => 'VARCHAR(100)',
                'confirmation_date' => 'TIMESTAMP NULL',
                'delivery_date' => 'TIMESTAMP NULL'
            ];

            $missingColumns = [];
            foreach ($deliveryColumns as $column => $type) {
                $result = $conn->query("SHOW COLUMNS FROM orders LIKE '$column'");
                if (!$result || $result->num_rows == 0) {
                    $missingColumns[$column] = $type;
                    echo "<p class='warning'>⚠ Column '$column' is missing</p>";
                } else {
                    echo "<p class='success'>✓ Column '$column' exists</p>";
                }
            }

            if (empty($missingColumns)) {
                echo '<p class="success">✓ All delivery columns already exist!</p>';
                $currentStep++;
            }
            echo '</div>';
            echo '<script>document.getElementById("progress").style.width = "' . ($currentStep/$totalSteps*100) . '%"; document.getElementById("progress").textContent = "' . round($currentStep/$totalSteps*100) . '%";</script>';

            // Step 4: Add missing columns
            if (!empty($missingColumns)) {
                echo '<div class="step">';
                echo '<h3>Step 4: Adding Missing Columns</h3>';

                foreach ($missingColumns as $column => $type) {
                    $sql = "ALTER TABLE orders ADD COLUMN $column $type";

                    if ($conn->query($sql)) {
                        echo "<p class='success'>✓ Successfully added column: $column</p>";
                    } else {
                        echo "<p class='error'>✗ Failed to add column '$column': " . $conn->error . "</p>";
                        $errors[] = "Failed to add column: $column";
                    }
                }

                if (empty($errors)) {
                    echo '<p class="success">✓ All missing columns have been added successfully!</p>';
                    $currentStep++;
                }
                echo '</div>';
            } else {
                echo '<div class="step">';
                echo '<h3>Step 4: Add Missing Columns</h3>';
                echo '<p class="info">✓ No columns needed to be added - system is already set up!</p>';
                $currentStep++;
                echo '</div>';
            }
            echo '<script>document.getElementById("progress").style.width = "' . ($currentStep/$totalSteps*100) . '%"; document.getElementById("progress").textContent = "' . round($currentStep/$totalSteps*100) . '%";</script>';

            // Step 5: Verify setup
            echo '<div class="step">';
            echo '<h3>Step 5: Verifying Setup</h3>';

            $allColumnsExist = true;
            foreach (array_keys($deliveryColumns) as $column) {
                $result = $conn->query("SHOW COLUMNS FROM orders LIKE '$column'");
                if (!$result || $result->num_rows == 0) {
                    $allColumnsExist = false;
                    echo "<p class='error'>✗ Verification failed: Column '$column' still missing</p>";
                }
            }

            if ($allColumnsExist) {
                echo '<p class="success">✓ All delivery columns verified successfully!</p>';
                $currentStep++;

                // Update existing orders to have pending status
                $updateResult = $conn->query("UPDATE orders SET delivery_status = 'pending' WHERE delivery_status IS NULL");
                if ($updateResult) {
                    echo '<p class="success">✓ Updated existing orders with default status</p>';
                }
            }
            echo '</div>';
            echo '<script>document.getElementById("progress").style.width = "100%"; document.getElementById("progress").textContent = "100%";</script>';

            // Final Summary
            echo '<div class="step" style="border-left-color: #28a745; background: #d4edda;">';
            echo '<h2 style="color: #28a745;">🎉 Setup Complete!</h2>';
            echo '<p>Your delivery system is now ready to use!</p>';
            echo '<h3 style="margin-top: 20px;">Next Steps:</h3>';
            echo '<ol>';
            echo '<li>Test the system with <a href="setup_and_test.php">setup_and_test.php</a></li>';
            echo '<li>Place a test order on the <a href="index.html">homepage</a></li>';
            echo '<li>View orders in the <a href="rider_dashboard.php">Rider Dashboard</a></li>';
            echo '<li>Track orders using <a href="track_order.php">Track Order</a> page</li>';
            echo '</ol>';

            echo '<div style="margin-top: 20px;">';
            echo '<a href="index.html" class="btn">🏠 Go to Homepage</a>';
            echo '<a href="rider_dashboard.php" class="btn">🛵 Rider Dashboard</a>';
            echo '<a href="setup_and_test.php" class="btn btn-success">🧪 Test System</a>';
            echo '</div>';
            echo '</div>';

            // Show database structure
            echo '<div class="step">';
            echo '<h3>Updated Database Structure</h3>';
            $result = $conn->query("DESCRIBE orders");
            if ($result) {
                echo '<pre>';
                echo "Table: orders\n";
                echo str_repeat("-", 80) . "\n";
                printf("%-20s %-30s %-10s %-10s\n", "Field", "Type", "Null", "Default");
                echo str_repeat("-", 80) . "\n";

                while ($row = $result->fetch_assoc()) {
                    printf("%-20s %-30s %-10s %-10s\n",
                        $row['Field'],
                        $row['Type'],
                        $row['Null'],
                        $row['Default'] ?? 'NULL'
                    );
                }
                echo '</pre>';
            }
            echo '</div>';

            $conn->close();

        } catch (Exception $e) {
            echo '<div class="step" style="border-left-color: #dc3545; background: #f8d7da;">';
            echo '<h3 style="color: #dc3545;">❌ Setup Failed</h3>';
            echo '<p class="error">' . htmlspecialchars($e->getMessage()) . '</p>';

            echo '<h4>Troubleshooting:</h4>';
            echo '<ul>';
            echo '<li>Make sure MySQL/XAMPP is running</li>';
            echo '<li>Check if database "' . DB_NAME . '" exists</li>';
            echo '<li>Run <a href="db_init.php">db_init.php</a> to create the database</li>';
            echo '<li>Check database credentials in db_config.php</li>';
            echo '</ul>';

            echo '<a href="db_init.php" class="btn btn-success">Initialize Database</a>';
            echo '<a href="check_db_structure.php" class="btn">Check Database</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
