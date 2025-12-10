<?php
session_start();

// Hardcoded admin credentials
$admin_username = "Hamza Hussain";
$admin_password = "123456789";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['admin-username'];
    $password = $_POST['admin-password'];

    // if the username and password matches
    if ($username == $admin_username && $password == $admin_password) {
        // Authentication successful
        $_SESSION['admin_logged_in'] = true; // Set session variable for logged-in status
        echo "<script>
                sessionStorage.setItem('admin_logged_in', 'true');
                alert('Login successful!');
                window.location.href='index.html#dashboard'; // Redirect to dashboard
              </script>";
        exit();
    } else {
        // if Authentication failed
        echo "<script>
                alert('Invalid username or password!');
                window.location.href='index.html'; // Redirect to home page
              </script>";
    }
}
?>
