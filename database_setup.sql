-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;

-- Create menu_items table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create order_items table
CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

-- Insert sample menu items
INSERT INTO menu_items (name, description, price, image_url) VALUES
('Margherita Pizza', 'Classic pizza with tomato, mozzarella, and basil', 12.99, 'https://via.placeholder.com/250x200?text=Margherita+Pizza'),
('Caesar Salad', 'Fresh romaine lettuce with Caesar dressing and croutons', 8.99, 'https://via.placeholder.com/250x200?text=Caesar+Salad'),
('Grilled Salmon', 'Fresh salmon grilled to perfection with lemon butter sauce', 18.99, 'https://via.placeholder.com/250x200?text=Grilled+Salmon'),
('Spaghetti Carbonara', 'Creamy pasta with bacon, eggs, and Parmesan cheese', 14.99, 'https://via.placeholder.com/250x200?text=Spaghetti+Carbonara'),
('Chocolate Cake', 'Decadent chocolate cake with chocolate frosting', 7.99, 'https://via.placeholder.com/250x200?text=Chocolate+Cake'),
('Tiramisu', 'Classic Italian dessert with mascarpone and espresso', 6.99, 'https://via.placeholder.com/250x200?text=Tiramisu');
