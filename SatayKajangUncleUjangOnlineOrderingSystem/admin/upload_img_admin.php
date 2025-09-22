<?php
session_start();
include '../connect.php';

// Dapatkan ID admin (contoh kalau simpan dalam session)
$admin_id = $_SESSION['admin_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $success_message = '';

    // Handle upload image
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "../uploads/admin/";
        $file_name = basename($_FILES['profile_image']['name']);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi jenis file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } else {
            // Upload
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update ke DB
                $stmt = $conn->prepare("UPDATE admin SET profile_image = ? WHERE admin_id = ?");
                $stmt->bind_param("si", $file_name, $admin_id);
                if ($stmt->execute()) {
                    $success_message = "Profile image updated successfully.";
                } else {
                    $errors[] = "Database error updating image.";
                }
                $stmt->close();
            } else {
                $errors[] = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Handle update profile info (kalau button Save ditekan)
    if (isset($_POST['update_profile'])) {
        $admin_name = $_POST['admin_name'];
        $phone_no   = $_POST['phone_no'];
        $address    = $_POST['address'];

        $stmt = $conn->prepare("UPDATE admin SET admin_name=?, phone_no=?, address=? WHERE admin_id=?");
        $stmt->bind_param("sssi", $admin_name, $phone_no, $address, $admin_id);

        if ($stmt->execute()) {
            $success_message .= " Profile info updated successfully.";
        } else {
            $errors[] = "Failed to update profile info.";
        }
        $stmt->close();
    }

    // Redirect balik ke page profile dengan mesej
    if (!empty($success_message)) {
        $_SESSION['success_message'] = $success_message;
    }
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }

    header("Location: profAdmin.php");
    exit();
}
