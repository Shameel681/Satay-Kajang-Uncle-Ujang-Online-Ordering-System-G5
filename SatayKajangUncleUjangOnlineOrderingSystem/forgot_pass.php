<?php
require_once 'connect.php';
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    $sql = "SELECT customer_id FROM customer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Generate token
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 min

        $update_sql = "UPDATE customer SET reset_token=?, reset_expires=? WHERE email=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $token_hash, $expiry, $email);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'toonpow43@gmail.com';
                $mail->Password   = 'mzyp uzsq aarf mmmq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('toonpow43@gmail.com', 'Satay Kajang Uncle Ujang');
                $mail->addAddress($email);

                $reset_link = "http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/reset_pass.php?token=" . $token;

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/forgot_pass.css">
</head>
<body>
    <div class="login-form">
        <h2>Forgot Password</h2>
        
        <?php if ($message): ?>
    <?php 
        $class = (strpos($message, 'sent') !== false) ? 'success' : 'error'; 
    ?>
    <div class="message-box <?= $class ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
            <a href="login.php" type="back" class="btn" style="text-align: center;">Back To Login</a>
        </form>
    </div>
</body>
</html>
