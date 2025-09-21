<?php
// change_staff.php
// Lokasi: /admin/ atau /staff/ ikut struktur awak. Sesuaikan require path.
require_once '../connect.php';

// pastikan session dimulakan (jika connect.php tak start session)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// pastikan staff logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

// ambil staff_id dari session (pastikan session key betul)
$staff_id = isset($_SESSION['staff_id']) ? (int) $_SESSION['staff_id'] : 0;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil input, trim untuk buang ruang kosong
    $current = isset($_POST['current_password']) ? trim($_POST['current_password']) : '';
    $newpass  = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm  = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // basic validations
    if ($newpass !== $confirm) {
        $message = "New password and confirmation do not match.";
    } elseif (strlen($newpass) < 8) {
        $message = "New password must be at least 8 characters.";
    } else {
        // Ambil hashed password dari DB (pastikan prepare berjaya)
        $sql = "SELECT password FROM staff WHERE staff_id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            // DB prepare failed
            $message = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("i", $staff_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if ($row && isset($row['password'])) {
                // verify current password
                if (password_verify($current, $row['password'])) {
                    // update to new hashed password
                    $new_hashed = password_hash($newpass, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE staff SET password = ? WHERE staff_id = ?";
                    $ustmt = $conn->prepare($update_sql);

                    if (!$ustmt) {
                        $message = "Database error: " . $conn->error;
                    } else {
                        $ustmt->bind_param("si", $new_hashed, $staff_id);
                        if ($ustmt->execute()) {
                            $message = "Password changed successfully!";
                            // optional: redirect kembali ke profile selepas berjaya
                            // header("Location: profStaff.php?msg=pass_changed");
                            // exit;
                        } else {
                            $message = "Error updating password. Please try again.";
                        }
                        $ustmt->close();
                    }
                } else {
                    $message = "Current password is incorrect.";
                }
            } else {
                $message = "Staff record not found. Please re-login.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Change Password (Staff)</title>
<link rel="stylesheet" href="../CSS/profCust.css">
<link rel="stylesheet" href="../CSS/change_staff.css">
</head>
<body>
  <div class="change-pass-container">
    <h2>Change Password</h2>

    <?php if (!empty($message)): ?>
      <div class="message-box"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" class="change-pass-form">
      <div class="form-group">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password" required>
      </div>
      <div class="form-group">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required minlength="8">
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
      </div>
      <button type="submit" class="btn">Update Password</button>
    </form>

    <p><a href="profStaff.php" class="back-btn">← Back to Profile</a></p>
  </div>
</body>
</html>
