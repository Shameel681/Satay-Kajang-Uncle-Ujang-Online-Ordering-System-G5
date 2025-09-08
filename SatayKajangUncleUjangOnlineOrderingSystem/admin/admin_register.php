<?php
// Include DB connection (adjust path since this file is inside /admin folder)
require_once '../connect.php';
require __DIR__ . '/../vendor/autoload.php'; // autoload PHPMailer from main path

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$message_type = '';

$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_name = $_POST['admin_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($admin_name) || empty($email) || empty($password)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists in admin table
        $check_sql = "SELECT admin_id FROM admin WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = 'Registration failed. The email is already registered.';
            $message_type = 'error';
        } else {
            // Generate verify token
            $verify_token = bin2hex(random_bytes(16));

            // Validation
            if (strlen($password) < 8) {
                $message = 'Password must be at least 8 characters long.';
                $message_type = 'error';
            } else {
                // Insert admin
                $insert_sql = "INSERT INTO admin (admin_name, email, password, is_verified, verify_token, created_at, updated_at) 
                               VALUES (?, ?, ?, 0, ?, NOW(), NOW())";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ssss", $admin_name, $email, $password_hash, $verify_token);

                if ($insert_stmt->execute()) {
                    // Send verification email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'toonpow43@gmail.com'; // your Gmail
                        $mail->Password   = 'mzyp uzsq aarf mmmq'; // Gmail app password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('youremail@gmail.com', 'Satay Kajang Uncle Ujang - Admin');
                        $mail->addAddress($email, $admin_name);

                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Admin Account';
                        $verify_link = "http://localhost/MASTER PROJECT - SATAY KAJANG UNCLE UJANG ONLINE ORDERING SYSTEM G05/SatayKajangUncleUjangOnlineOrderingSystem/admin/admin_verify.php?token=" . $verify_token;
                        $mail->Body    = "
                            <h3>Hi $admin_name,</h3>
                            <p>You have been registered as an Admin. Please verify your email using the link below:</p>
                            <a href='$verify_link'>$verify_link</a>
                        ";

                        $mail->send();
                        $message = 'Admin registration successful! Please check your email to verify your account.';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        $message = "Registration success but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Registration failed. Please try again.';
                    $message_type = 'error';
                }
                $insert_stmt->close();
            }
        }
        $check_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/register.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="../index.php">Satay Kajang Uncle Ujang - Admin</a></h1>
            <nav>
                <ul>
                    <?php if ($is_loggedin): ?>
                        <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="admin_register.php" class="btn">Register</a></li>
                        <li><a href="admin_login.php" class="btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="registration-form">
            <div class="container">
                <h2>Admin Registration</h2>
                <p>Register as an admin to manage the system.</p>

                <?php if (!empty($message)): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <form class="register-form" action="admin_register.php" method="POST">
                    <div class="form-group">
                        <label for="admin_name">Name:</label>
                        <input type="text" id="admin_name" name="admin_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" minlength="8" required>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-bottom">
                <p>© 2025 Satay Kajang Uncle Ujang. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
