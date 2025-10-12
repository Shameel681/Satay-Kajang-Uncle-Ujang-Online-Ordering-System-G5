<?php
require_once '../connect.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_image'];
    $upload_dir = '../uploads/admin/';
    
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
        $new_filename = $admin_id . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Delete old image if exists
            $old_stmt = $conn->prepare("SELECT profile_image FROM admin WHERE admin_id=?");
            $old_stmt->bind_param("i", $admin_id);
            $old_stmt->execute();
            $old_result = $old_stmt->get_result();
            $old_admin = $old_result->fetch_assoc();
            
            if ($old_admin && !empty($old_admin['profile_image']) && $old_admin['profile_image'] !== $new_filename) {
                $old_path = $upload_dir . $old_admin['profile_image'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            $old_stmt->close();
            
            // Update database
            $stmt = $conn->prepare("UPDATE admin SET profile_image=? WHERE admin_id=?");
            $stmt->bind_param("si", $new_filename, $admin_id);
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

header("Location: profAdmin.php");
exit;
?>