<?php
// Include the database connection file which starts the session
require_once '../connect.php';  // adjust path if connect.php is outside /admin folder

// Initialize variables for messages and login status
$message = '';
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;

// If an admin is already logged in, redirect them to the admin dashboard
if ($is_loggedin) {
    header("Location: admin_dashboard.php");
    exit;
}

// Check if a login attempt has been made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT admin_id, admin_name, email, password FROM admin WHERE email = ?";
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
                $_SESSION['admin_loggedin'] = true;
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['admin_name'] = $row['admin_name'];
                $_SESSION['admin_email'] = $row['email'];

                // Update last login timestamp
                $update_sql = "UPDATE admin SET last_login = NOW() WHERE admin_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                if ($update_stmt) {
                    $update_stmt->bind_param("i", $row['admin_id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                }

                // Redirect to admin dashboard
                header("Location: admin_dashboard.php"); 
                exit;
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "No admin account found with that email.";
        }
        $stmt->close();
    } else {
        $message = "Failed to prepare login statement.";
    }
}

// Close the connection
if (isset($conn)) {
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/admin_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Header HTML -->
    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="../index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
               <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../customer/menu.php">Menu</a></li>
                    <li><a href="../customer/about.php">About us</a></li>
                    <li><a href="../customer/contact.php">Contact us</a></li>
                    <?php if ($is_loggedin): ?>
                        <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <?php else: ?>
                    <li>
                        <a href="../login.php" class="btn">Customer Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
    <?php if (!empty($message)): ?>
                <div class="message-box error">
                    <?php echo htmlspecialchars($message); ?>
                </div>

                <script>
                // auto fade out after 4s
                setTimeout(() => {
                    let box = document.querySelector(".message-box.error");
                    if (box) {
                    box.classList.remove("show");
                    }
                }, 4000);

                setTimeout(() => {
                    let box = document.querySelector(".message-box.error");
                    if (box) {
                    box.style.opacity = "0";
                    box.style.transform = "translateX(0%) translateY(0px)";
                    }
                }, 4000); // hide after 4s
                </script>
    <?php endif; ?>
    </div>


    <main>
        <section class="login-form">
            <div class="container">
                <h2>Admin Login</h2>
                <p>Log in as Admin to manage the system.</p>

                <form class="login-form" action="" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <p class="forgot-link" style="text-align: right; margin-top: -10px; font-size: 0.9rem; color: #666;">
                            <a href="admin_forgot.php" style="color: #f07b3f; text-decoration: none; font-weight: bold;">Forgot password?</a>
                    </p>

                    <button type="submit" class="btn">Login</button>
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
            <a href="../index.php">Home</a><br>
            <a href="../customer/menu.php">Menu</a><br>
            <a href="../customer/about.php">About Us</a><br>
            <a href="../customer/contact.php">Contact Us</a><br>
          </div>

          <!-- Right Column -->
          <div class="footer-right">
            <h3>Staff & Admin</h3>
            <a href="../staff/staff_login.php">Staff Login</a><br>
            <a href="../admin/admin_login.php">Admin Login</a>
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
