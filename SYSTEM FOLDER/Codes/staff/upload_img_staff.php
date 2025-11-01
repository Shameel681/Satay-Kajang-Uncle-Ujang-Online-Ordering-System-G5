<?php
require_once '../connect.php';
session_start();

if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

if (isset($_FILES['staff_image']) && $_FILES['staff_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['staff_image'];
    $upload_dir = '../Uploads/staff/';
    
    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, GIF allowed.";
    } elseif ($file['size'] > $max_size) {
        $_SESSION['error_message'] = "File too large. Maximum 2MB allowed.";
    } else {
        // Generate unique filename
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = $staff_id . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Delete old image if exists
            $old_stmt = $conn->prepare("SELECT staff_image FROM staff WHERE staff_id=?");
            $old_stmt->bind_param("i", $staff_id);
            $old_stmt->execute();
            $old_result = $old_stmt->get_result();
            $old_staff = $old_result->fetch_assoc();
            
            if ($old_staff && !empty($old_staff['staff_image']) && $old_staff['staff_image'] !== $new_filename) {
                $old_path = $upload_dir . $old_staff['staff_image'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            $old_stmt->close();
            
            // Update database
            $stmt = $conn->prepare("UPDATE staff SET staff_image=? WHERE staff_id=?");
            $stmt->bind_param("si", $new_filename, $staff_id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Profile image uploaded successfully!";
            } else {
                $_SESSION['error_message'] = "Image uploaded but database update failed.";
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "Failed to upload image.";
        }
    }
} else {
    $_SESSION['error_message'] = "No image selected or upload error.";
}

header("Location: profStaff.php");
exit;
?>