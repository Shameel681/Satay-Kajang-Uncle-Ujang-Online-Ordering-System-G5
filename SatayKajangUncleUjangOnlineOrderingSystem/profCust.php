<?php
// Include the database connection file.
// session_start() is expected to be in this file.
require_once 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Get customer info from session
$customer_id = $_SESSION['customer_id'];

// Define the variable for the header
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Prepare a SELECT statement to get the customer's current data
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Check if the form was submitted from a successful update
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message']);
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/profCust.css">
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
                    <a href="login.php" class="btn active">Login</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main>
  <section class="profile">
    <div class="profile-container">
      <?php if (isset($success_message)): ?>
          <div class="message-box success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      <?php if (isset($error_message)): ?>
          <div class="message-box error"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <div class="profile-card">
        <div class="profile-header">
          <i class="fa-solid fa-user-circle profile-icon"></i>
          <h2><?php echo htmlspecialchars($customer['name']); ?></h2>
          <p>Customer Profile</p>
        </div>
        
        <!-- Profile Form -->
<!-- Profile Form -->
<form action="profUpdate.php" method="POST" id="profile-form">
  <div class="form-group">
    <label>Name:</label>
    <input type="text" name="name" id="name" 
           value="<?php echo htmlspecialchars($customer['name']); ?>" 
           disabled>
  </div>

  <div class="form-group">
    <label>Email:</label>
    <input type="email" name="email" id="email" 
           value="<?php echo htmlspecialchars($customer['email']); ?>" 
           disabled>
  </div>

  <div class="form-group">
    <label>Phone:</label>
    <input type="tel" name="phone_no" id="phone_no" 
           value="<?php echo htmlspecialchars($customer['phone_no']); ?>" 
           disabled>
  </div>

  <div class="form-group">
    <label>Address:</label>
    <textarea name="address" id="address" rows="3" disabled><?php echo htmlspecialchars($customer['address']); ?></textarea>
  </div>

  <div class="profile-actions">
    <button type="button" id="edit-btn" class="btn">Edit Profile</button>
    <button type="submit" id="save-btn" class="btn" name="update_profile" style="display:none;">Save Changes</button>
    <button type="button" id="cancel-btn" class="btn" style="display:none;">Cancel</button>
    <a href="change_pass.php" class="btn">Change Password</a>
  </div>
</form>

<script>
  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const inputs = document.querySelectorAll("#profile-form input, #profile-form textarea");

  // Simpan value asal
  let originalValues = {};
  inputs.forEach(input => {
    originalValues[input.name] = input.value;
  });

  // Klik Edit → enable semua field
  editBtn.addEventListener("click", () => {
    inputs.forEach(input => input.disabled = false);
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
  });

  // Klik Cancel → reset balik ke value asal
  cancelBtn.addEventListener("click", () => {
    inputs.forEach(input => {
      input.value = originalValues[input.name];
      input.disabled = true;
    });

    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";
  });
</script>

       
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



<script src="script/profCust.js"></script>
</body>
</html>
