<?php
require_once 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Initialize error variable
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone_no']);
    $address = trim($_POST['address']);

    if ($name && $email && $phone_no && $address) {
        $stmt = $conn->prepare("UPDATE customer SET name=?, email=?, phone_no=?, address=? WHERE customer_id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $customer_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Profile has successfully been updated.";
            header("Location: profCust.php");  // Redirect to avoid resubmission on refresh
            exit;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please fill all fields.";
    }
}

// Fetch current customer data
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM customer WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Profile</title>
  <link rel="stylesheet" href="CSS/profCust.css" />
  <link rel="stylesheet" href="style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
  />
  <style>
    /* Toast style */
    #toast {
      visibility: hidden;
      min-width: 250px;
      margin-left: -125px;
      background-color: #4CAF50;
      color: white;
      text-align: center;
      border-radius: 5px;
      padding: 16px;
      position: fixed;
      z-index: 9999;
      left: 50%;
      top: 30px;
      font-size: 17px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      transition: visibility 0s, opacity 0.5s ease-in-out;
      opacity: 0;
    }

    #toast.show {
      visibility: visible;
      opacity: 1;
    }
  </style>
</head>
<body>
<header>
  <div class="container">
    <div class="logo-and-title">
      <div class="logo-circle">
        <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo" />
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
          <h2 id="profileName"><?php echo htmlspecialchars($customer['name']); ?></h2>
          <p>Customer Profile</p>
        </div>

        <?php if ($error): ?>
          <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="profile-details" id="profileDetails">
          <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone_no']); ?></p>
          <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($customer['address'])); ?></p>
        </div>

        <form
          id="editProfileForm"
          action="profCust.php"
          method="POST"
          style="display: none; text-align: left; margin-top: 20px;"
        >
          <label for="name">Name:</label><br />
          <input
            type="text"
            id="name"
            name="name"
            value="<?php echo htmlspecialchars($customer['name']); ?>"
            required
          /><br /><br />

          <label for="email">Email:</label><br />
          <input
            type="email"
            id="email"
            name="email"
            value="<?php echo htmlspecialchars($customer['email']); ?>"
            required
          /><br /><br />

          <label for="phone_no">Phone Number:</label><br />
          <input
            type="text"
            id="phone_no"
            name="phone_no"
            value="<?php echo htmlspecialchars($customer['phone_no']); ?>"
            required
          /><br /><br />

          <label for="address">Address:</label><br />
          <textarea
            id="address"
            name="address"
            rows="3"
            required
          ><?php echo htmlspecialchars($customer['address']); ?></textarea
          ><br /><br />

          <button type="submit" class="btn">Save</button>
          <button type="button" class="btn" id="cancelEditBtn" style="background:#999; margin-left:10px;">
            Cancel
          </button>
        </form>

        <div class="profile-actions" style="margin-top: 25px;">
          <button class="btn" id="editProfileBtn"><i class="fa-solid fa-pen"></i> Edit Profile</button>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Toast popup -->
<div id="toast"><?php
    if (isset($_SESSION['success_message'])) {
        echo htmlspecialchars($_SESSION['success_message']);
        unset($_SESSION['success_message']); // Clear message after use
    }
?></div>

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

<script src="script/profCust.js"></script>
<script>
  // Show toast if it has content
  const toast = document.getElementById('toast');
  if (toast.textContent.trim() !== "") {
    toast.classList.add('show');
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  }
</script>
</body>
</html>
