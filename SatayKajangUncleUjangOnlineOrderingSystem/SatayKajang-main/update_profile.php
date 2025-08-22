<?php
// Include the database connection file which starts the session
require_once 'connect.php'; 

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user's email from the session to identify the user
    $user_email = $_SESSION['email'];

    // Sanitize and get the new data from the form
    $new_name = htmlspecialchars(trim($_POST['name']));
    $new_phone = htmlspecialchars(trim($_POST['phone']));
    $new_address = htmlspecialchars(trim($_POST['address']));

    // Prepare an UPDATE statement
    // Note: The column names in the query must match your database table
    $sql = "UPDATE customer SET name = ?, phone_no = ?, address = ? WHERE email = ?";

    // Use a prepared statement for secure database updates
    if ($stmt = $conn->prepare($sql)) {
        // Bind the new values and the user's email to the statement
        $stmt->bind_param("ssss", $new_name, $new_phone, $new_address, $user_email);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Update the session variables with the new information
            $_SESSION['name'] = $new_name;
            $_SESSION['phone'] = $new_phone;
            $_SESSION['address'] = $new_address;

            // Redirect back to the profile page with a success message
            header("Location: profCust.php?status=success");
            exit;
        } else {
            // Handle update error
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle prepare error
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();

} else {
    // If the form was not submitted via POST, redirect them back to the profile page
    header("Location: profCust.php");
    exit;
}
?>