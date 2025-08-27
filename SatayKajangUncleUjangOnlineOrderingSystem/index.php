<?php
// Include the database connection file which starts the session
require_once 'connect.php'; 

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$customer_name = $is_loggedin ? htmlspecialchars($_SESSION['name']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Home</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
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
                    <li>
                        <a href="logout.php" class="btn">Logout</a>
                    </li>
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

    <main>
        <section class="hero">
        <div class="hero-content">
            <?php if ($is_loggedin): ?>
                <h1>Welcome, <?php echo $customer_name; ?></h1>
            <?php else: ?>
                <h1>Belum Try Belum Tahu,<br>Sudah Try Ingat Selalu...</h1>
            <?php endif; ?>
            <p class="tagline">Ramuan Rempah Ratus Turun Temurun</p>
            <a href="menu.php" class="btn">View Our Menu</a>
        </div>
    </section>

        <section class="featured">
            <div class="container">
                <h2>OUR SIGNATURE</h2>
                <div class="features">
                    <div class="feature-item">
                        <img src="image/satay ayam.png" width="100%" height="auto">
                        <h3>Satay Ayam</h3>
                        <p>Ayam diperap rempah rahsia, memanggang harum semerbak</p>
                    </div>
                    <div class="feature-item">
                        <img src="image/satay daging.jpg" width="100%" height="auto">
                        <h3>Satay Daging</h3>
                        <p>Daging dihiris halus, lembut dan penuh rasa</p>
                    </div>
                    <div class="feature-item">
                        <img src="image/satay perut.jpg" width="100%" height="auto">
                        <h3>Satay Perut</h3>
                        <p>Perut direndam rempah, kenyal dan berperisa unik</p>
                    </div>
                    <div class="feature-item">
                        <img src="image/Satay kambing.jpg" width="100%" height="auto">
                        <h3>Satay Kambing</h3>
                        <p>Kambing dipanggang tepat, wangi dan tiada bau</p>
                    </div>
                    <div class="feature-item">
                        <img src="image/Kuah kacang.jpg" width="100%" height="auto">
                        <h3>Kuah Kacang</h3>
                        <p>Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat</p>
                    </div>
                    <div class="feature-item">
                        <img src="image/Nasi Impit lagi.jpg" width="100%" height="auto">
                        <h3>Nasi Impit</h3>
                        <p>Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.</p>
                    </div>
                </div>
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
        <a href="news.php">News</a>
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