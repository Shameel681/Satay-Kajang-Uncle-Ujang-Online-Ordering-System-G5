<?php

// This entire block of code is the PHP logic that runs on the server.
// It connects to the database and processes the form data.

// Database connection details
$servername = "localhost";
$username = "root"; // Your XAMPP username
$password = ""; // Your XAMPP password
$dbname = "SKUUOOS";

$message = '';
$message_type = '';

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $message = 'An internal server error occurred. Please try again later.';
        $message_type = 'error';
    } else {
        // Get the form data
        $full_name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone_no = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';

        // Basic server-side validation
        if (empty($full_name) || empty($email) || empty($phone_no) || empty($password)) {
            $message = 'All fields are required.';
            $message_type = 'error';
        } else {
            // Hash the password for security
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists
            $check_sql = "SELECT id FROM customer WHERE email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $message = 'Registration failed. The email is already registered.';
                $message_type = 'error';
            } else {
                // Use a prepared statement to prevent SQL injection
                $sql = "INSERT INTO customer (full_name, email, phone_no, password_hash) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $full_name, $email, $phone_no, $password_hash);

                if ($stmt->execute()) {
                    $message = 'Registration successful! You can now log in.';
                    $message_type = 'success';
                } else {
                    $message = 'Registration failed. Please try again.';
                    $message_type = 'error';
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Register</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/register.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>



    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="index.html">Satay Kajang Uncle Ujang</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="menu.html">Menu</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="register.html" class="active btn">Register as Customer</a></li>
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

                <form class="register-form" id="registerForm" action="" method="POST">
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
