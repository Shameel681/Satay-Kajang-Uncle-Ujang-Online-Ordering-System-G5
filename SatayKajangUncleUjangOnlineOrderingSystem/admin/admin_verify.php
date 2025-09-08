<?php
require '../connect.php'; // since admin_verify.php is inside /admin

$message = '';
$message_type = ''; // 'success' or 'error'

if (isset($_GET['token']) && $_GET['token'] !== '') {
    $token = $_GET['token'];

    // Find admin with token
    $sql = "SELECT admin_id, is_verified FROM admin WHERE verify_token = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ((int)$row['is_verified'] === 1) {
            $message = "✅ Your admin email is already verified. You can now login.";
            $message_type = "success";
        } else {
            // Update is_verified
            $update_sql = "UPDATE admin 
                           SET is_verified = 1, verify_token = NULL 
                           WHERE admin_id = ? AND verify_token = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $row['admin_id'], $token);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                $message = "✅ Your admin email has been verified! You can now login.";
                $message_type = "success";
            } else {
                $message = "❌ Verification failed. Please try again.";
                $message_type = "error";
            }
            $update_stmt->close();
        }
    } else {
        $message = "❌ Invalid or expired token.";
        $message_type = "error";
    }

    $stmt->close();
} else {
    $message = "❌ No token provided.";
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Email Verification</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="stylesheet" href="../CSS/verify.css">
</head>
<body>
    <main>
        <div class="verify-container">
            <h2>Admin Email Verification</h2>
            <div class="message-box <?php echo htmlspecialchars($message_type); ?>">
                <?php echo $message; ?>
            </div>

            <?php if ($message_type === "success"): ?>
                <a href="admin_login.php" class="btn">Go to Login</a>
            <?php else: ?>
                <a href="admin_register.php" class="btn">Back to Register</a>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
