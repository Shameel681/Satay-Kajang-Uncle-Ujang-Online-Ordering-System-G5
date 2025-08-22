<?php
// Include the database connection file which starts the session
require_once 'connect.php'; 

// Initialize variables for messages and login status
$message = '';
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// If a user is already logged in, redirect them to the home page
if ($is_loggedin) {
    header("Location: index.php");
    exit;
}

// Check if a login attempt has been made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT customer_id, name, email, password FROM customer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify the hashed password
            if (password_verify($password, $row['password'])) {
                // Password is correct, so start a new session
                $_SESSION['loggedin'] = true;
                $_SESSION['customer_id'] = $row['customer_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];

                // Redirect to the home page
                header("Location: index.php"); 
                exit;
            } else {
                // Password is not valid, set an error message
                $message = "Invalid password.";
            }
        } else {
            // Email not found, set an error message
            $message = "No account found with that email.";
        }
        $stmt->close();
    } else {
        $message = "Failed to prepare login statement.";
    }
}

// Check if a connection exists before trying to close it
if (isset($conn)) {
    $conn->close();
}
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
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
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php" <?php if ($current_page == 'profCust.php') echo 'class="active"'; ?>>Profile</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="auth-links">
                    <?php if ($is_loggedin): ?>
                        <li><a href="logout.php" class="btn">Logout</a></li>
                    <?php else: ?>
                        <li><a href="register.php" class="btn">Register as Customer</a></li>
                        <li><a href="login.php" class="btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
    </header>

   

    <main>
        <section class="login-form">
            <div class="container">
                <h2>Customer Login</h2>
                <p>Log in to your account to place your order!</p>
                
                <?php if (!empty($message)): ?>
                <div class="message-box error">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form class="login-form" action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Login</button>
                    <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
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
    <script src="script/dashboard.js"></script>
</body>
</html>