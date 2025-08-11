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
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if ($is_loggedin): ?>
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
                <h1>Welcome, <?php echo $customer_name; ?>!</h1>
                <?php else: ?>
                <h1>Belum Try Belum Tahu,<br>
                    Sudah Try Ingat Selalu...</h1>
                <?php endif; ?>
                <p class="tagline">Ramuan Rempah Ratus Turun Temurun</p>
                <a href="menu.php class="btn">View Our Menu</a>
            </div>
        </section>

        <section class="featured">
            <div class="container">
                <h2>OUR SIGNATURE</h2>
                <div class="features">
                    <div class="feature-item">
                        <img src="image/satay ayam.png" width="100%" height="71%">
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