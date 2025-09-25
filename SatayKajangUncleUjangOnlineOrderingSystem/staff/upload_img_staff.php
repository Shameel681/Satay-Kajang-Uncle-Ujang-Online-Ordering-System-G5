<?php
session_start();
require_once '../connect.php';

// Staff login check
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];
$success = false;

// Handle image upload
if (isset($_FILES['staff_image']) && $_FILES['staff_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    if (in_array($_FILES['staff_image']['type'], $allowed_types)) {
        $ext = pathinfo($_FILES['staff_image']['name'], PATHINFO_EXTENSION);
        $new_name = "staff_" . $staff_id . "." . $ext;
        $upload_dir = "../uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($_FILES['staff_image']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE staff SET staff_image = ? WHERE staff_id = ?");
            $stmt->bind_param("si", $new_name, $staff_id);
            $stmt->execute();
            $stmt->close();
            $success = true;
        }
    }
}

// Handle profile info update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone_no'] ?? '';
    $address = $_POST['address'] ?? '';

    $stmt = $conn->prepare("UPDATE staff SET name=?, phone_no=?, address=? WHERE staff_id=?");
    $stmt->bind_param("sssi", $name, $phone, $address, $staff_id);

    $stmt->execute();
    $stmt->close();
    $success = true;
}

if ($success) {
    $_SESSION['success_message'] = "Profile updated successfully!";
} else {
    $_SESSION['error_message'] = "No changes made.";
}

// Redirect balik ke staff profile page
header("Location: profStaff.php");
exit;
?>
