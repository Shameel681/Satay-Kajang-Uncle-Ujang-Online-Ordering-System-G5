<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['update_profile'])) {
    $customer_id = $_SESSION['customer_id'];
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone_no = htmlspecialchars(trim($_POST['phone_no']));
    $address = htmlspecialchars(trim($_POST['address']));
    $profile_image = htmlspecialchars(trim($_POST['profile_image']));

    $stmt = $conn->prepare("UPDATE customer SET name = ?, email = ?, phone_no = ?, address = ?, profile_image = ? WHERE customer_id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $profile_image, $customer_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating profile.";
    }

    $stmt->close();
    header("Location: ../customer/profCust.php");
    exit;
}
?>
