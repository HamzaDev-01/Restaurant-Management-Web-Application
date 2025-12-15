<?php
// Centralized Database Configuration
// All PHP files should include this file for database connections

// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'restaurant_db_temp');

// Function to create database connection with better error handling
function getDatabaseConnection() {
    // Enable mysqli error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // Check for specific connection errors
        if (strpos($e->getMessage(), 'actively refused') !== false) {
            die("ERROR: MySQL server is not running. Please start your MySQL/XAMPP/WAMP server and try again.<br><br>Steps to fix:<br>1. Open XAMPP Control Panel<br>2. Start MySQL service<br>3. Refresh this page");
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            die("ERROR: Database '" . DB_NAME . "' does not exist. Please run db_init.php first to create the database.<br><br>Visit: <a href='db_init.php'>db_init.php</a>");
        } else {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
}

// Function to check if database exists
function checkDatabaseExists() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        $result = $conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
        $exists = $result->num_rows > 0;
        $conn->close();
        return $exists;
    } catch (mysqli_sql_exception $e) {
        return false;
    }
}
?>
