<?php
require_once 'connect.php';

$message = '';
$message_type = '';
$token = $_GET['token'] ?? '';

if ($token) {
    $token_hash = hash("sha256", $token);

    $sql = "SELECT customer_id, reset_expires FROM customer WHERE reset_token=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token_hash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['reset_expires']) > time()) {
            // Form submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                if ($password === $confirm) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);

                    $update = "UPDATE customer SET password=?, reset_token=NULL, reset_expires=NULL WHERE customer_id=?";
                    $u_stmt = $conn->prepare($update);
                    $u_stmt->bind_param("si", $hash, $row['customer_id']);
                    $u_stmt->execute();

                    if ($u_stmt->affected_rows > 0) {
                        $message = "✅ Password updated successfully! You can now login.";
                        $message_type = "success";
                    } else {
                        $message = "❌ Failed to update password. Try again.";
                        $message_type = "error";
                    }
                } else {
                    $message = "❌ Passwords do not match.";
                    $message_type = "error";
                }
            }
        } else {
            $message = "❌ Reset link has expired.";
            $message_type = "error";
        }
    } else {
        $message = "❌ Invalid reset token.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="CSS/reset_pass.css">
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php if ($message): ?>
            <div class="message-box <?= htmlspecialchars($message_type) ?>"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($message_type !== "success"): ?>
        <form method="POST">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Reset</button>
        </form>
        <?php else: ?>
            <a href="login.php" class="btn">Go to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
