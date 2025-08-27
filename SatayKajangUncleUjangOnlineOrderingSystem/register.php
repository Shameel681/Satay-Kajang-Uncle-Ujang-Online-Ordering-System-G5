<?php
// Include the database connection file, which starts the session
require_once 'connect.php';
require 'vendor/autoload.php'; // autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables for messages
$message = '';
$message_type = '';

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($phone_no) || empty($password)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check_sql = "SELECT customer_id FROM customer WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = 'Registration failed. The email is already registered.';
            $message_type = 'error';
        } else {
            // generate verify token
            $verify_token = bin2hex(random_bytes(16));

            // insert new user with verify_token & is_verified=0
            $insert_sql = "INSERT INTO customer (name, email, phone_no, password, is_verified, verify_token) VALUES (?, ?, ?, ?, 0, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $name, $email, $phone_no, $password_hash, $verify_token);

            if ($insert_stmt->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'toonpow43@gmail.com'; // your Gmail
                    $mail->Password   = 'mzyp uzsq aarf mmmq';   // your Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Recipients
                    $mail->setFrom('youremail@gmail.com', 'Satay Kajang Uncle Ujang');
                    $mail->addAddress($email, $name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email Address';
                    $verify_link = "http://localhost/MASTER PROJECT - SATAY KAJANG UNCLE UJANG ONLINE ORDERING SYSTEM G05/SatayKajangUncleUjangOnlineOrderingSystem/verify.php?token=" . $verify_token;
                    $mail->Body    = "
                        <h3>Hi $name,</h3>
                        <p>Thank you for registering. Please click the link below to verify your email:</p>
                        <a href='$verify_link'>$verify_link</a>
                    ";

                    $mail->send();
                    $message = 'Registration successful! Please check your email to verify your account.';
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
    <title>Register</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="contact.php">Contact us</a></li>
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php">Profile</a></li>
                    <li>
                        <a href="logout.php" class="btn">Logout</a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="register.php" class="btn active">Register</a>
                    </li>
                    <li>
                        <a href="login.php" class="btn">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="registration-form">
            <div class="container">
                <h2>Customer Registration</h2>
                <p>Register to become a customer and enjoy our delicious satay!</p>

                <?php if (!empty($message)): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <form class="register-form" id="registerForm" action="register.php" method="POST" target="_self">

                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Register</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer HTML -->
<footer>
  <div class="footer-container">
    <div class="footer-row">
      <!-- Left Column -->
      <div class="footer-left">
        <h3>Explore Our Page</h3>
        <a href="index.php">Home</a><br>
        <a href="about.php">About Us</a><br>
        <a href="menu.php">Menu</a><br>
        <a href="contact.php">Contact Us</a>
      </div>

      <!-- Right Column -->
      <div class="footer-right">
        <h3>Staff & Admin</h3>
        <a href="staff_login.php">Staff Login</a><br>
        <a href="admin_login.php">Admin Login</a>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2025 Satay Kajang Uncle Ujang. All rights reserved.</p>
      <div class="social-links">
        <a href="#"><i class="fa-brands fa-facebook"></i></a>
        <a href="#"><i class="fa-brands fa-twitter"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
      </div>
    </div>
  </div>
</footer>
</body>
</html>