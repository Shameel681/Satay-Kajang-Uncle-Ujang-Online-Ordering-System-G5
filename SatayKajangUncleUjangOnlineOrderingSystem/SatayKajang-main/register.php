<?php
// Include the database connection file, which starts the session
require_once 'connect.php';

// Initialize variables for messages
$message = '';
$message_type = '';

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and use the null coalescing operator to avoid errors
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic server-side validation to ensure all required fields are filled
    if (empty($name) || empty($email) || empty($phone_no) || empty($password)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } else {
        // Hash the password for security before storing it in the database
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists in the database using a prepared statement
        $check_sql = "SELECT customer_id FROM customer WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = 'Registration failed. The email is already registered.';
            $message_type = 'error';
        } else {
            // Use a prepared statement to insert new user data
            $insert_sql = "INSERT INTO customer (name, email, phone_no, password) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $name, $email, $phone_no, $password_hash);

            if ($insert_stmt->execute()) {
                // Get the ID of the newly registered user
                $customer_id = $insert_stmt->insert_id;

                // **NEW FEATURE: Automatically log in the user after successful registration**
                $_SESSION['loggedin'] = true;
                $_SESSION['customer_id'] = $customer_id;
                $_SESSION['name'] = $name;

                // Redirect to the home page or a success page
                header("Location: index.php");
                exit;
            } else {
                $message = 'Registration failed. Please try again.';
                $message_type = 'error';
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
    // Close the database connection at the end of the script
    $conn->close();
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
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php">Profile</a></li>
                    <li>
                        <a href="logout.php" class="btn">Logout</a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="register.php" class="btn active">Register as Customer</a>
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