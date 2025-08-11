<?php
// Start the session
session_start();

// Database connection details
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "skuuoos"; // The name of your database

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    // If the connection fails, stop the script and display an error message
    die("Connection failed: " . $conn->connect_error);
}
?>