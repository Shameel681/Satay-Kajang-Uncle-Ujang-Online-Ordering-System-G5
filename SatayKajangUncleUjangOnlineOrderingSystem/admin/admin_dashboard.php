<?php
// admin_dashboard.php
session_start();

// Example session check for admin (modify based on your login system)
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;

if (!$is_loggedin) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Satay Kajang Uncle Ujang</title>
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link rel="stylesheet" href="../css/admin_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

  <!-- Header -->
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
                  <li><a href="admin_dashboard.php">Dashboard</a></li>
                  <li><a href="admin_logout.php" class="btn">Logout</a></li>
              </ul>
          </nav>
      </div>
  </header>

  <!-- Main Admin Dashboard Content -->
  <main class="dashboard-container">
      <h2>Welcome, Admin!</h2>
      <p>Here you can manage the system.</p>

      <div class="dashboard-cards">
          <div class="card">
              <h3>Manage Customers</h3>
              <p>View, edit, or remove customer accounts.</p>
              <a href="manage_customers.php" class="btn">Go</a>
          </div>
          <div class="card">
              <h3>Manage Menu</h3>
              <p>Add, update, or delete menu items.</p>
              <a href="manage_menu.php" class="btn">Go</a>
          </div>
          <div class="card">
              <h3>Manage Orders</h3>
              <p>Track and manage customer orders.</p>
              <a href="manage_orders.php" class="btn">Go</a>
          </div>
          <div class="card">
              <h3>Reports</h3>
              <p>View sales and performance reports.</p>
              <a href="reports.php" class="btn">Go</a>
          </div>
      </div>
  </main>

  <!-- Footer -->
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
