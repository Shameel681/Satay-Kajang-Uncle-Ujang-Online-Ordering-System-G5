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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Contact Us</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
                    <?php else: ?>
                    <li>
                        <a href="register.php" class="btn">Register</a>
                    </li>
                    <li>
                        <a href="login.php" class="btn">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <?php if (isset($_SESSION['feedback_success']) || isset($_SESSION['feedback_errors'])): ?>
        <div id="feedbackToast" class="toast <?php echo isset($_SESSION['feedback_success']) ? 'success' : 'error'; ?>">
            <span>
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
            </span>
            <button class="toast-close" aria-label="Close">&times;</button>
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
                        <p>Phone: 011-1138-3819 / 01162226128<br>
                        <p>Email: <a href="mailto:toonnpow3@gmail.com">toonnpow3@gmail.com</a></p>
                    </div>
                </div>

                
                    <div class="map-container">
                       <iframe 
                       width="100%" 
                       src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.534602808614!2d101.8576!3d2.9143!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdcac7a6c91c2b%3A0x5c89cbbdf8a6c5a1!2s1%2C%20Jalan%20Tps%201%2F6%2C%20Taman%20Pelangi%20Semenyih%2C%2043500%20Semenyih%2C%20Selangor!5e0!3m2!1sen!2smy!4v1735463899983!5m2!1sen!2smy" 
                       style="border:0;" 
                       height="400" 
                       loading="lazy" 
                       allowfullscreen="" 
                        referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
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

     <!-- Footer HTML -->
<footer>
  <div class="footer-container">
    <div class="footer-row">
      <!-- Left Column -->
      <div class="footer-left">
        <h3>Explore Our Page</h3>
        <a href="index.php">Home</a><br>
        <a href="menu.php">Menu</a><br>
        <a href="about.php">About Us</a><br>
        <a href="contact.php">Contact Us</a><br>
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

    <script src="script/feedback.js"></script>
</body>
</html>