# Restaurant Management Web Application

A full-stack restaurant management system with customer ordering and admin dashboard features.

## Prerequisites

Before running this project, ensure you have the following installed:

1. **PHP** (version 7.4 or higher)
   - Download from: https://www.php.net/downloads
   - For Windows: https://windows.php.net/download/

2. **MySQL/MariaDB** (or XAMPP/WAMP/MAMP which includes both PHP and MySQL)
   - XAMPP (recommended for Windows): https://www.apachefriends.org/
   - MySQL: https://dev.mysql.com/downloads/

3. **Web Browser** (Chrome, Firefox, Edge, etc.)

## Installation & Setup

### Method 1: Using XAMPP (Recommended for Windows)

1. **Install XAMPP**
   - Download and install XAMPP from https://www.apachefriends.org/
   - Install it in the default location (C:\xampp)

2. **Copy Project Files**
   ```bash
   # Copy the entire project folder to XAMPP's htdocs directory
   # The path should be: C:\xampp\htdocs\Restaurant-Management-Web-Application
   ```

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

4. **Initialize Database**
   ```bash
   # Open terminal/command prompt and navigate to project directory
   cd C:\xampp\htdocs\Restaurant-Management-Web-Application\Restaurant-Management-Web-Application

   # Run the database initialization script
   php db_init.php

   # Run the migration script to add new columns
   php migrate_orders_table.php
   ```

5. **Access the Application**
   - Open your browser and go to:
   - Customer Interface: http://localhost/Restaurant-Management-Web-Application/Restaurant-Management-Web-Application/index.html
   - Admin Dashboard: Click "Admin Dashboard" button and login with:
     - Username: `Hamza Hussain`
     - Password: `123456789`

### Method 2: Using PHP Built-in Server (Quick Testing)

1. **Ensure MySQL is Running**
   ```bash
   # Make sure MySQL service is running on your system
   # For XAMPP: Start MySQL from XAMPP Control Panel
   ```

2. **Navigate to Project Directory**
   ```bash
   cd "c:\Users\Hamza\Desktop\GitHub\Restaurant-Management-Web-Application\Restaurant-Management-Web-Application"
   ```

3. **Initialize Database**
   ```bash
   php db_init.php
   php migrate_orders_table.php
   ```

4. **Start PHP Development Server**
   ```bash
   php -S localhost:8000
   ```

5. **Access the Application**
   - Open your browser and go to: http://localhost:8000/index.html
   - Admin Dashboard: Click "Admin Dashboard" and login with credentials above

## Project Structure

```
Restaurant-Management-Web-Application/
├── index.html                    # Main customer interface
├── dashboard.php                 # Admin dashboard page
├── authenticate.php              # Admin authentication
├── db_init.php                   # Database initialization
├── migrate_orders_table.php      # Database migration for new fields
├── save_order.php               # Order processing
├── fetch_order_history.php      # Fetch orders for admin
├── fetch_menu.php               # Fetch menu items
├── manage_menu.php              # Menu CRUD operations
├── script.js                    # Frontend JavaScript
└── style.css                    # Styling (if present)
```

## Features

### Customer Side
- Browse menu items with images and prices
- Add items to cart with quantity selection
- View cart summary with total
- Checkout with customer details:
  - Name
  - Email
  - Delivery Address
  - Phone Number
- Order confirmation

### Admin Side
- Secure login (Username: Hamza Hussain, Password: 123456789)
- View all orders with complete customer information
- Order details include:
  - Order ID
  - Customer Name
  - Email
  - Address
  - Phone Number
  - Items ordered
  - Order date/time
- Manage menu items (Add, Edit, Delete)
- Real-time order tracking

## Database Configuration

Default database settings (can be modified in PHP files):
- Host: `localhost`
- Username: `root`
- Password: `` (empty)
- Database: `restaurant_db_temp`

To change these settings, edit the following files:
- db_init.php
- save_order.php
- fetch_order_history.php
- authenticate.php
- manage_menu.php
- fetch_menu.php
- migrate_orders_table.php

## Troubleshooting

### 1. "Connection failed" Error
- Ensure MySQL service is running
- Check database credentials in PHP files
- Verify MySQL is running on port 3306

### 2. "Database not found" Error
```bash
# Run the initialization script again
php db_init.php
```

### 3. Missing Customer Fields in Admin Dashboard
```bash
# Run the migration script
php migrate_orders_table.php
```

### 4. Port 8000 Already in Use
```bash
# Use a different port
php -S localhost:8080
# Then access: http://localhost:8080/index.html
```

### 5. PHP Command Not Found
- Add PHP to your system PATH
- Or use full path: `C:\xampp\php\php.exe db_init.php`

## Testing the Application

1. **Place a Test Order**
   - Go to http://localhost:8000/index.html
   - Add items to cart
   - Fill in checkout details
   - Click "Place Order"

2. **View Order in Admin**
   - Click "Admin Dashboard"
   - Login with admin credentials
   - Navigate to "Recent Orders" section
   - Verify all customer details are displayed

## Development Notes

- Customer data is temporarily stored in browser localStorage
- Form validation is enabled (all fields required)
- Email field uses HTML5 email validation
- Phone field accepts various formats
- Address field is a textarea for multi-line input

## Security Notes

⚠️ **Important**: This is a development version. Before deploying to production:
1. Change admin credentials in authenticate.php
2. Implement proper password hashing
3. Add CSRF protection
4. Use prepared statements for all SQL queries
5. Add input sanitization and validation
6. Enable HTTPS
7. Implement proper session management

## Support

For issues or questions, please check:
1. Ensure all prerequisites are installed
2. Verify database is initialized
3. Check browser console for JavaScript errors
4. Review PHP error logs

## License

This project is for educational purposes.
