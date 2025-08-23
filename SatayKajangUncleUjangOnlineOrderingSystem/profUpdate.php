<?php
// Start the session and include the database connection file
session_start();
require_once 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if the form was submitted
if (isset($_POST['update_profile'])) {
    // Get data from the form
    $customer_id = $_SESSION['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];

    // Validate and sanitize inputs
    $name = htmlspecialchars(trim($name));
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone_no = htmlspecialchars(trim($phone_no));
    $address = htmlspecialchars(trim($address));

    // Prepare and execute the UPDATE statement
    // Using prepared statements is a security best practice to prevent SQL injection
    $stmt = $conn->prepare("UPDATE customer SET name = ?, email = ?, phone_no = ?, address = ? WHERE customer_id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $customer_id);

    if ($stmt->execute()) {
        // Update successful
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        // Update failed
        $_SESSION['error_message'] = "Error updating profile. Please try again.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the profile page
    header("Location: profCust.php");
    exit;
} else {
    // If the form was not submitted, redirect to the profile page
    header("Location: profCust.php");
    exit;
}
?>