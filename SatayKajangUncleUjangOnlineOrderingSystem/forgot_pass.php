<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/login.css"> <!-- Reuse login.css for similar styling -->
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
                    <li><a href="about.php">About</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profCust.php">Profile</a></li>
                    <li><a href="register.php" class="btn">Register</a></li>
                    <li><a href="login.php" class="btn active">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="login-form"> <!-- Reuse class for styling -->
            <div class="container">
                <h2>Forgot Password</h2>
                <p>Enter your email to reset your password.</p>
                
                <?php if (!empty($message)): ?>
                <div class="message-box <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form class="login-form" action="" method="POST"> <!-- Reuse form class -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn">Reset Password</button>
                    <p class="register-link">Remember your password? <a href="login.php">Login here</a></p>
                </form>
            </div>
        </section>
    </main>

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
</body>
</html>

<?php
// Processing code for forgot_pass.php
require_once 'connect.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $message = 'Email is required.';
        $message_type = 'error';
    } else {
        // Check if email exists
        $sql = "SELECT customer_id FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate reset token
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Update DB with token (assuming columns exist)
            $update_sql = "UPDATE customer SET reset_token = ?, reset_expires = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sss", $token, $expires, $email);
            $update_stmt->execute();

            // Send email (placeholder - configure mail server)
            $reset_link = "http://yourdomain.com/reset_pass.php?token=" . $token;
            $email_body = "Click this link to reset your password: " . $reset_link;
            mail($email, "Password Reset", $email_body);

            $message = 'Password reset link sent to your email.';
            $message_type = 'success';
        } else {
            $message = 'No account found with that email.';
            $message_type = 'error';
        }
        $stmt->close();
    }
    $conn->close();
}
?>