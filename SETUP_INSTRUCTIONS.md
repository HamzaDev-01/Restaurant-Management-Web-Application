# Restaurant Management Application - Setup Instructions

## Prerequisites

Before running this application, you need:

1. **XAMPP** (or WAMP/MAMP) - For Apache and MySQL server
2. **Web Browser** - Chrome, Firefox, or Edge
3. **Text Editor** (optional) - VS Code, Sublime Text, etc.

## Installation Steps

### Step 1: Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP to `C:\xampp` (default location)
3. Complete the installation

### Step 2: Start MySQL Server

1. Open **XAMPP Control Panel**
2. Click **Start** button next to **MySQL** (or **MariaDB**)
3. Wait until the status shows **Running** (green background)
4. Optionally, start **Apache** server as well

**Common Issues:**
- If MySQL won't start, check if port 3306 is being used by another application
- Check Windows Services to ensure no other MySQL instances are running

### Step 3: Setup the Database

1. Make sure MySQL is running in XAMPP
2. Open your web browser
3. Navigate to: `http://localhost/Restaurant-Management-Web-Application/db_init.php`
4. You should see a success message:
   ```
   ✓ Database setup completed successfully!
   ✓ Database: restaurant_db_temp
   ✓ Tables created: menu_items, orders, order_items
   ✓ Sample menu items inserted
   ```

**Alternative Method (Command Line):**
```bash
cd C:\Users\Hamza\Desktop\GitHub\Restaurant-Management-Web-Application\Restaurant-Management-Web-Application
php db_init.php
```

### Step 4: Run the Application

1. Open your browser
2. Navigate to: `http://localhost/Restaurant-Management-Web-Application/index.html`
3. The application should load without errors

## Troubleshooting

### Error: "No connection could be made because the target machine actively refused it"

**Solution:**
- MySQL server is not running
- Open XAMPP Control Panel and start MySQL
- Wait for it to show "Running" status

### Error: "Unknown database 'restaurant_db_temp'"

**Solution:**
- Database hasn't been created yet
- Run `db_init.php` as described in Step 3 above

### Error: "Access denied for user 'root'@'localhost'"

**Solution:**
- Your MySQL has a different password
- Edit `db_config.php` and update the `DB_PASS` constant with your MySQL root password

### Menu Items Not Showing

**Solution:**
1. Ensure database is initialized (run `db_init.php`)
2. Check browser console for JavaScript errors
3. Verify MySQL is running in XAMPP

## File Structure

```
Restaurant-Management-Web-Application/
├── index.html              # Main application page
├── dashboard.php           # Admin dashboard
├── db_config.php          # Centralized database configuration (NEW)
├── db_init.php            # Database initialization script
├── fetch_menu.php         # Fetches menu items
├── save_order.php         # Saves customer orders
├── fetch_order_history.php # Fetches order history
├── manage_menu.php        # API for menu CRUD operations
├── authenticate.php       # Admin authentication
└── SETUP_INSTRUCTIONS.md  # This file
```

## Database Configuration

All database connections now use a centralized configuration file: `db_config.php`

Default settings:
- **Host:** localhost
- **Username:** root
- **Password:** (empty)
- **Database:** restaurant_db_temp

To change these settings, edit `db_config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Add your password here if needed
define('DB_NAME', 'restaurant_db_temp');
```

## Admin Access

**Username:** Hamza Hussain
**Password:** 123456789

To change admin credentials, edit `authenticate.php`

## Features

- View menu items
- Add items to cart
- Place orders with customer information
- Admin dashboard for managing menu items
- Order history tracking

## Support

If you encounter any issues:

1. Check that XAMPP MySQL is running
2. Verify database exists in phpMyAdmin: `http://localhost/phpmyadmin`
3. Check browser console for errors (F12)
4. Review error messages - they now provide helpful guidance

## Next Steps

After successful setup:

1. Browse the menu at `index.html`
2. Access admin dashboard at `dashboard.php`
3. Place test orders to verify functionality
4. Check order history in admin dashboard
