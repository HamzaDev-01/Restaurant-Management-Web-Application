<?php
// Database setup script - runs once to initialize the database

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'restaurant_db_temp';

// Connect to MySQL server (without database selected)
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");

// Select the database
if (!$conn->select_db($dbname)) {
    die("Error selecting database: " . $conn->error);
}

// Create menu_items table
$createMenuSql = "CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createMenuSql)) {
    die("Error creating menu_items table: " . $conn->error);
}

// Create orders table
$createOrdersSql = "CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createOrdersSql)) {
    die("Error creating orders table: " . $conn->error);
}

// Create order_items table
$createOrderItemsSql = "CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
)";

if (!$conn->query($createOrderItemsSql)) {
    die("Error creating order_items table: " . $conn->error);
}

// Insert sample menu items
$insertSql = "INSERT INTO menu_items (name, description, price, image_url) VALUES
    ('Margherita Pizza', 'Classic pizza with tomato, mozzarella, and basil', 12.99, 'https://via.placeholder.com/250x200?text=Margherita+Pizza'),
    ('Caesar Salad', 'Fresh romaine lettuce with Caesar dressing and croutons', 8.99, 'https://via.placeholder.com/250x200?text=Caesar+Salad'),
    ('Grilled Salmon', 'Fresh salmon grilled to perfection with lemon butter sauce', 18.99, 'https://via.placeholder.com/250x200?text=Grilled+Salmon'),
    ('Spaghetti Carbonara', 'Creamy pasta with bacon, eggs, and Parmesan cheese', 14.99, 'https://via.placeholder.com/250x200?text=Spaghetti+Carbonara'),
    ('Chocolate Cake', 'Decadent chocolate cake with chocolate frosting', 7.99, 'https://via.placeholder.com/250x200?text=Chocolate+Cake'),
    ('Tiramisu', 'Classic Italian dessert with mascarpone and espresso', 6.99, 'https://via.placeholder.com/250x200?text=Tiramisu')";

if (!$conn->query($insertSql)) {
    die("Error inserting sample data: " . $conn->error);
}

echo "✓ Database setup completed successfully!\n";
echo "✓ Database: restaurant_db_temp\n";
echo "✓ Tables created: menu_items, orders, order_items\n";
echo "✓ Sample menu items inserted\n";

$conn->close();
?>
