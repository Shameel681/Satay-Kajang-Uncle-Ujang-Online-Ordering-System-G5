<?php
session_start();
require_once '../connect.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

if (isset($_POST['update_profile'])) {
    $staff_id = $_SESSION['staff_id'];

    // Match form field names (from profStaff.php)
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_no = htmlspecialchars(trim($_POST['phone_no']));
    $address = htmlspecialchars(trim($_POST['address']));

    // Prevent unintended email change (since email should be readonly in form)
    $stmt = $conn->prepare("SELECT email FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $email = $row['email']; // overwrite with DB value
    $stmt->close();

    // Update query (only editable fields: name, phone_no, address)
    $stmt = $conn->prepare("UPDATE staff SET name = ?, phone_no = ?, address = ? WHERE staff_id = ?");
    if (!$stmt) {
        $_SESSION['error_message'] = "SQL Error: " . $conn->error;
        header("Location: profStaff.php");
        exit;
    }

    $stmt->bind_param("sssi", $name, $phone_no, $address, $staff_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating profile.";
    }

    $stmt->close();
    header("Location: profStaff.php");
    exit;
}
?>
