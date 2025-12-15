<?php
// Migration script to add delivery tracking columns to existing orders table
require_once 'db_config.php';

$conn = getDatabaseConnection();

// Check if columns already exist
$checkSql = "SHOW COLUMNS FROM orders LIKE 'delivery_status'";
$result = $conn->query($checkSql);

if ($result->num_rows == 0) {
    // Add delivery_status column
    $alterSql1 = "ALTER TABLE orders
        ADD COLUMN delivery_status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending' AFTER order_date";

    if ($conn->query($alterSql1)) {
        echo "✓ Added delivery_status column\n";
    } else {
        die("Error adding delivery_status column: " . $conn->error);
    }
} else {
    echo "✓ delivery_status column already exists\n";
}

// Check and add rider_name column
$checkSql2 = "SHOW COLUMNS FROM orders LIKE 'rider_name'";
$result2 = $conn->query($checkSql2);

if ($result2->num_rows == 0) {
    $alterSql2 = "ALTER TABLE orders
        ADD COLUMN rider_name VARCHAR(100) AFTER delivery_status";

    if ($conn->query($alterSql2)) {
        echo "✓ Added rider_name column\n";
    } else {
        die("Error adding rider_name column: " . $conn->error);
    }
} else {
    echo "✓ rider_name column already exists\n";
}

// Check and add confirmation_date column
$checkSql3 = "SHOW COLUMNS FROM orders LIKE 'confirmation_date'";
$result3 = $conn->query($checkSql3);

if ($result3->num_rows == 0) {
    $alterSql3 = "ALTER TABLE orders
        ADD COLUMN confirmation_date TIMESTAMP NULL AFTER rider_name";

    if ($conn->query($alterSql3)) {
        echo "✓ Added confirmation_date column\n";
    } else {
        die("Error adding confirmation_date column: " . $conn->error);
    }
} else {
    echo "✓ confirmation_date column already exists\n";
}

// Check and add delivery_date column
$checkSql4 = "SHOW COLUMNS FROM orders LIKE 'delivery_date'";
$result4 = $conn->query($checkSql4);

if ($result4->num_rows == 0) {
    $alterSql4 = "ALTER TABLE orders
        ADD COLUMN delivery_date TIMESTAMP NULL AFTER confirmation_date";

    if ($conn->query($alterSql4)) {
        echo "✓ Added delivery_date column\n";
    } else {
        die("Error adding delivery_date column: " . $conn->error);
    }
} else {
    echo "✓ delivery_date column already exists\n";
}

echo "\n✓ Migration completed successfully!\n";
echo "✓ Orders table now has delivery tracking columns\n";

$conn->close();
?>
