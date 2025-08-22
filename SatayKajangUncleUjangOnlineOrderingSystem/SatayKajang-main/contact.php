<?php

require_once 'connect.php';

// Start a session if one is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$is_loggedin = !empty($customer_id);

// Initialize variables
$customer_name = "Guest";
$customer_email = null;

// Only fetch customer data if a user is logged in
if ($is_loggedin) {
    // Prepare and execute the query to get customer name and email
    $stmt_customer = $conn->prepare("SELECT name, email FROM customer WHERE customer_id = ?");
    $stmt_customer->bind_param("i", $customer_id);
    $stmt_customer->execute();
    $result = $stmt_customer->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt_customer->close();

    // If customer data is found, update the variables
    if ($customer_data) {
        $customer_name = htmlspecialchars($customer_data['name']);
        $customer_email = htmlspecialchars($customer_data['email']);
    }
}
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Contact Us</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="css/contact.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
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

    

    <?php if (isset($_SESSION['feedback_success']) || isset($_SESSION['feedback_errors'])): ?>
        <div id="feedbackToast" class="toast <?php echo isset($_SESSION['feedback_success']) ? 'success' : 'error'; ?>">
            <?php
                if (isset($_SESSION['feedback_success'])) {
                    echo htmlspecialchars($_SESSION['feedback_success']);
                    unset($_SESSION['feedback_success']);
                } else {
                    foreach ($_SESSION['feedback_errors'] as $error) {
                        echo htmlspecialchars($error) . '<br>';
                    }
                    unset($_SESSION['feedback_errors']);
                }
            ?>
        </div>
    <?php endif; ?>

    <main>
        <section class="all-contact">
            <div class="hero-overlay">
                <div class="container">
                    <h2>Get in Touch with Us!</h2>
                    <p>Taste the tradition, feel the flavor – let's connect.</p>
                    <a href="https://wa.me/+601162226128" class="btn" target="_blank">Contact Now</a>
                </div>
            </div>
        </section>

        <section class="contact" id="contact-form">
            <div class="container">
                <h2>Reach Out to Us</h2>
                
                <div class="contact-info">
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Visit Us</h3>
                        <p>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-clock"></i>
                        <h3>Opening Hours</h3>
                        <p>Monday-Friday: 6.00pm - 11.00pm<br>Saturday-Sunday: 5.00pm - 11pm<br><br><b>Close will be announce.</b></p>
                    </div>
                    <div class="contact-card">
                        <i class="fas fa-phone"></i>
                        <h3>Contact Number</h3>
                        <p>Phone: 011-1138-3819 or 01162226128<br>Email: <a href="mailto:toonnpow3@gmail.com">toonnpow3@gmail.com</a></p>
                    </div>
                </div>
                
                <form class="contact-form" id="contactForm" action="process_feedback.php" method="POST">
                    <h3>Send Us Feedback</h3>
                    <?php if ($is_loggedin): ?>
                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" value="<?php echo htmlspecialchars($customer_name); ?>" readonly>
                            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" value="<?php echo htmlspecialchars($customer_email); ?>" readonly>
                            <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($customer_email); ?>">
                        </div>
                    <?php else: ?>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="guest_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="guest_email" required>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="message">Your Feedback:</label>
                        <textarea id="message" name="feedback" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Feedback</button>
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
    <script src="script/feedback.js"></script>
</body>
</html>