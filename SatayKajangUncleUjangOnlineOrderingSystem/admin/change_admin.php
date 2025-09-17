<?php
require_once '../connect.php';


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$admin_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $newpass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newpass !== $confirm) {
        $message = "New password and confirmation do not match.";
    } else {
        $sql = "SELECT password FROM admin WHERE admin_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && password_verify($current, $row['password'])) {
            $hashed = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE admin SET password=? WHERE admin_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed, $admin_id);
            if ($stmt->execute()) {
                $message = "Password changed successfully!";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="../CSS/profCust.css">
    <link rel="stylesheet" href="../CSS/change_admin.css">
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="change-pass-container">
        <h2>Change Password</h2>
        <?php if ($message): ?>
            <div class="message-box"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" class="change-pass-form">
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Update Password</button>
        </form>
        <a href="profAdmin.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
    </div>
</body>
</html>
