<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (isset($_POST['update_profile'])) {
    $admin_id = $_SESSION['admin_id'];
    $name = htmlspecialchars(trim($_POST['username']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_no = htmlspecialchars(trim($_POST['phone_no']));
    $address = htmlspecialchars(trim($_POST['address']));

    $stmt = $conn->prepare("UPDATE admin SET admin_name = ?, email = ?, phone_no = ?, address = ? WHERE admin_id = ?");
    if (!$stmt) {
        $_SESSION['error_message'] = "SQL Error: " . $conn->error;
        header("Location: profAdmin.php");
        exit;
    }

    $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $admin_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating profile.";
    }

    $stmt->close();
    header("Location: profAdmin.php");
    exit;
}
?>
