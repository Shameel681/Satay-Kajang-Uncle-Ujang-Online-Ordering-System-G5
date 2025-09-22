<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$success = false;

// Handle image upload
if (isset($_FILES['customer_image']) && $_FILES['customer_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    if (in_array($_FILES['customer_image']['type'], $allowed_types)) {
        $ext = pathinfo($_FILES['customer_image']['name'], PATHINFO_EXTENSION);
        $new_name = "cust_" . $customer_id . "." . $ext;
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($_FILES['customer_image']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE customer SET customer_image = ? WHERE customer_id = ?");
            $stmt->bind_param("si", $new_name, $customer_id);
            $stmt->execute();
            $stmt->close();
            $success = true;
        }
    }
}

// Handle profile info update (optional)
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone_no'] ?? '';
    $address = $_POST['address'] ?? '';

    $stmt = $conn->prepare("UPDATE customer SET name=?, phone_no=?, address=?, customer_image=?  WHERE customer_id=?");
    $stmt->bind_param("sssi", $name, $phone, $address, $customer_id);
    $stmt->execute();
    $stmt->close();
    $success = true;
}

if ($success) {
    $_SESSION['success_message'] = "Profile updated successfully!";
} else {
    $_SESSION['error_message'] = "No changes made.";
}

header("Location: ../customer/profCust.php");
exit;
?>
