<?php
// Include the database connection file
include 'connect.php';

// Start a session if one is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

// If logged in user
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $feedback = trim($_POST['feedback']);

    if (empty($feedback)) {
        $errors[] = "Feedback cannot be empty.";
    }

    if (empty($errors)) {
        // First, get the customer's name and email from the 'customer' table.
        // The column names are corrected to 'name' and 'email' as per your database.
        $stmt_customer = $conn->prepare("SELECT name, email FROM customer WHERE customer_id = ?");
        $stmt_customer->bind_param("i", $customer_id);
        $stmt_customer->execute();
        $result = $stmt_customer->get_result();
        $customer_data = $result->fetch_assoc();
        $stmt_customer->close();

        if ($customer_data) {
            // Use the correct array keys 'name' and 'email'
            $customer_name = $customer_data['name'];
            $customer_email = $customer_data['email'];

            // Now, insert the feedback using the retrieved name and email
            $stmt = $conn->prepare("INSERT INTO feedback_customer (customer_name, customer_email, feedback, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $customer_name, $customer_email, $feedback);

            if ($stmt->execute()) {
                $_SESSION['feedback_success'] = "Thank you for your feedback!";
            } else {
                // Log the actual error for debugging
                error_log("Error saving feedback for logged-in user: " . $stmt->error);
                $errors[] = "Error saving feedback. Please try again.";
            }
            $stmt->close();
        } else {
            $errors[] = "Customer data not found.";
        }
    }
} else { // If guest user
    $guest_name = trim($_POST['guest_name']);
    $guest_email = trim($_POST['guest_email']);
    $feedback = trim($_POST['feedback']);

    if (empty($guest_name) || empty($guest_email) || empty($feedback)) {
        $errors[] = "All fields are required for guests.";
    }

    if (empty($errors)) {
        // Corrected the column name from 'feedback_text' to 'feedback'
        $stmt = $conn->prepare("INSERT INTO feedback_guest (guest_name, guest_email, feedback, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $guest_name, $guest_email, $feedback);

        if ($stmt->execute()) {
            $_SESSION['feedback_success'] = "Thank you for your feedback!";
        } else {
            // Log the actual error for debugging
            error_log("Error saving feedback for guest user: " . $stmt->error);
            $errors[] = "Error saving feedback. Please try again.";
        }
        $stmt->close();
    }
}

if (!empty($errors)) {
    $_SESSION['feedback_errors'] = $errors;
}

// Redirect back to the contact page
header("Location: contact.php");
exit;
?>