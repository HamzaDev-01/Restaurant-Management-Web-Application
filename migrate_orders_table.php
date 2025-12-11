<?php
// Migration script to add email, address, and phone_number columns to orders table

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'restaurant_db_temp';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add email column if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM orders LIKE 'email'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN email VARCHAR(100) NOT NULL DEFAULT '' AFTER customer_name");
    echo "✓ Added email column to orders table\n";
} else {
    echo "✓ Email column already exists\n";
}

// Add address column if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM orders LIKE 'address'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN address TEXT NOT NULL AFTER email");
    echo "✓ Added address column to orders table\n";
} else {
    echo "✓ Address column already exists\n";
}

// Add phone_number column if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM orders LIKE 'phone_number'");
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN phone_number VARCHAR(20) NOT NULL DEFAULT '' AFTER address");
    echo "✓ Added phone_number column to orders table\n";
} else {
    echo "✓ Phone number column already exists\n";
}

echo "\n✓ Migration completed successfully!\n";

$conn->close();
?>
