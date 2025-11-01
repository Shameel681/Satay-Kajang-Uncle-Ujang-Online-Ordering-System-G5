<?php
require_once '../connect.php';
require '../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // ✅ SELECT statement with error check
    $sql = "SELECT admin_id FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error (SELECT): " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // ✅ Generate reset token
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 min

        $update_sql = "UPDATE admin SET reset_token=?, reset_expires=? WHERE email=?";
        $update_stmt = $conn->prepare($update_sql);

        if (!$update_stmt) {
            die("SQL Error (UPDATE): " . $conn->error);
        }

        $update_stmt->bind_param("sss", $token_hash, $expiry, $email);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            // ✅ Send reset email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'toonpow43@gmail.com';
                $mail->Password   = 'mzyp uzsq aarf mmmq'; // ✅ gunakan App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('toonpow43@gmail.com', 'Satay Kajang Uncle Ujang');
                $mail->addAddress($email);

                $reset_link = "http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/admin/admin_reset.php?token=" . $token;

                $mail->isHTML(true);
                $mail->Subject = 'Admin Password Reset Request';
                $mail->Body    = "
                    <h3>Password Reset</h3>
                    <p>Click the link below to reset your password:</p>
                    <a href='$reset_link'>$reset_link</a>
                    <p><i>This link will expire in 30 minutes.</i></p>
                ";

                $mail->send();
                $message = "Password reset email sent! Please check your inbox.";
            } catch (Exception $e) {
                $message = "Email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Failed to generate reset token.";
        }
    } else {
        $message = "Email not found in our system.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Forgot Password</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/admin_forgot.css">
</head>
<body>

    <main>
        <section class="forgot-form">
            <div class="container">
                <h2>Forgot Password</h2>
                <p>Enter your email to receive a password reset link.</p>

                <?php if ($message): ?>
                    <?php $class = (strpos($message, 'sent') !== false) ? 'success' : 'error'; ?>
                    <div class="message-box <?= $class ?>"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email" placeholder="Enter your admin email" required>
                    </div>
                    <button type="submit" class="btn">Reset Password</button>

                <!-- Back to Login as Button -->
                <a href="admin_login.php"  class="back-btn">← Back to Login</a>
                </form>
            </div>
        </section>
    </main>

</body>
</html>
