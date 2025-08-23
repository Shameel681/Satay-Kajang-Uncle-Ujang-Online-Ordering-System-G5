<?php
require_once 'connect.php'; 

$message = '';
$message_type = 'error'; // Default to error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $message = "Please enter your email.";
    } else {
        // Check if email exists
        $sql = "SELECT customer_id FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate reset token
            $token = bin2hex(random_bytes(32)); // Token unik 64 char
            $token_hash = password_hash($token, PASSWORD_DEFAULT); // Hash token untuk simpan di DB
            $expiry = date("Y-m-d H:i:s", time() + 3600); // Expiry 1 jam

            // Update DB dengan token dan expiry
            $update_sql = "UPDATE customer SET reset_token = ?, reset_expiry = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sss", $token, $expiry, $email);
            $update_stmt->execute();

            // Hantar email dengan PHPMailer
            require 'includes/PHPMailer-master/src/Exception.php';
            require 'includes/PHPMailer-master/src/PHPMailer.php';
            require 'includes/PHPMailer-master/src/SMTP.php';


            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Server settings (Ganti dengan details anda)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // SMTP server Gmail
                $mail->SMTPAuth = true;
                $mail->Username = 'yourgmail@gmail.com'; // Ganti dengan email Gmail anda
                $mail->Password = 'yourpassword or app_password'; // Ganti dengan password atau App Password
                $mail->SMTPSecure = 'tls'; // atau 'ssl'
                $mail->Port = 587; // atau 465 untuk ssl

                // Recipients
                $mail->setFrom('yourgmail@gmail.com', 'Satay Kajang Uncle Ujang');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "
                    <p>Hi,</p>
                    <p>You requested a password reset. Click the link below to reset your password:</p>
                    <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/SatayKajang-main/reset_password.php?token=$token&email=$email'>Reset Password</a>
                    <p>The link will expire in 1 hour.</p>
                    <p>If you didn't request this, ignore this email.</p>
                ";

                $mail->send();
                $message = "A reset link has been sent to your email.";
                $message_type = 'success';
            } catch (Exception $e) {
                $message = "Failed to send email. Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "No account found with that email.";
        }
        $stmt->close();
    }
}

$conn->close();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Forgot Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/login.css"> <!-- Reuse login CSS for consistency -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Header sama seperti login.php -->
    <header class="header1">
        <div class="logo-and-title">
            <div class="logo-circle">
                <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
            </div>
            <h1><a href="index.php">Satay Kajang Uncle Ujang</a></h1>
        </div>
    </header>

    <header class="header2">
        <nav>
            <ul class="nav-links">
                <li><a href="index.php" <?php if ($current_page == 'index.php') echo 'class="active"'; ?>>Home</a></li>
                <li><a href="menu.php" <?php if ($current_page == 'menu.php') echo 'class="active"'; ?>>Menu</a></li>
                <li><a href="about.php" <?php if ($current_page == 'about.php') echo 'class="active"'; ?>>About Us</a></li>
                <li><a href="contact.php" <?php if ($current_page == 'contact.php') echo 'class="active"'; ?>>Contact Us</a></li>
            </ul>
            <ul class="auth-links">
                <li><a href="register.php" class="btn">Register as Customer</a></li>
                <li><a href="login.php" class="btn">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="login-form"> <!-- Reuse class untuk style sama -->
            <div class="container">
                <h2>Forgot Password</h2>
                <p>Enter your email to receive a password reset link.</p>
                
                <?php if (!empty($message)): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form class="login-form" action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn">Send Reset Link</button>
                    <p class="register-link"><a href="login.php">Back to Login</a></p>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer sama seperti login.php -->
    <footer>
        <div class="container">
            <p>© 2016 SATAY KAJANG UNCLE UJANG. All rights reserved.</p>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>
    <script src="script/dashboard.js"></script>
</body>
</html>