<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $new_name = "cust_" . $customer_id . "." . $ext;
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE customer SET profile_image = ? WHERE customer_id = ?");
            $stmt->bind_param("si", $new_name, $customer_id);
            $stmt->execute();
            $stmt->close();
            $_SESSION['success_message'] = "Profile image updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to upload image.";
        }
    } else {
        $_SESSION['error_message'] = "Only JPG and PNG allowed.";
    }
} else {
    $_SESSION['error_message'] = "No image uploaded.";
}

 header("Location: ../customer/profCust.php");
 exit;
?>
