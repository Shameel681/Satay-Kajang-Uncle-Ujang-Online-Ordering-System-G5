<?php
// Include DB connection
require_once 'connect.php';

// Check login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Get customer info from session / database
$customer_id = $_SESSION['customer_id']; 
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Profile</title>
  <link rel="stylesheet" href="CSS/profCust.css">
  <link rel="stylesheet" href="style.css">
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
        <li><a href="about.php">About</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="profCust.php" class="active">Profile</a></li>
        <li><a href="logout.php" class="btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

<main>
  <section class="profile">
    <div class="profile-container">
      <div class="profile-card">
        <div class="profile-header">
          <i class="fa-solid fa-user-circle profile-icon"></i>
          <h2><?php echo htmlspecialchars($customer['name']); ?></h2>
          <p>Customer Profile</p>
        </div>
        <div class="profile-details">
          <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone_no']); ?></p>
          <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></p>
        </div>
        <div class="profile-actions">
          <button class="btn" id="editProfileBtn"><i class="fa-solid fa-pen"></i> Edit Profile</button>
        </div>
      </div>
    </div>
  </section>
</main>

<footer>
  <div class="container">
    <p>© 2016 SATAY KAJANG UNCLE UJANG. All rights reserved.</p>
    <div class="social-links">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
  </div>
</footer>

<script src="profCust.js"></script>
</body>
</html>
