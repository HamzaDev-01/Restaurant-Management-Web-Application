<?php
// Script to check and display database structure
require_once 'db_config.php';

try {
    $conn = getDatabaseConnection();

    echo "<h2>Database Structure Check</h2>";
    echo "<h3>Orders Table Structure:</h3>";

    $result = $conn->query("DESCRIBE orders");

    if ($result) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        $hasDeliveryStatus = false;
        $hasRiderName = false;

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";

            if ($row['Field'] === 'delivery_status') $hasDeliveryStatus = true;
            if ($row['Field'] === 'rider_name') $hasRiderName = true;
        }

        echo "</table>";

        echo "<h3>Migration Status:</h3>";
        if ($hasDeliveryStatus && $hasRiderName) {
            echo "<p style='color: green; font-weight: bold;'>✓ Delivery columns exist - Migration completed!</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>✗ Delivery columns missing - Need to run migration!</p>";
            echo "<p><a href='migrate_delivery_columns.php' style='background: #ff6f61; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Migration Now</a></p>";
        }
    }

    // Check sample data
    echo "<h3>Sample Orders:</h3>";
    $ordersResult = $conn->query("SELECT * FROM orders LIMIT 5");
    if ($ordersResult && $ordersResult->num_rows > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>";
        $fields = $ordersResult->fetch_fields();
        foreach ($fields as $field) {
            echo "<th>" . $field->name . "</th>";
        }
        echo "</tr>";

        $ordersResult->data_seek(0);
        while ($row = $ordersResult->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found in database</p>";
    }

    $conn->close();

} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
