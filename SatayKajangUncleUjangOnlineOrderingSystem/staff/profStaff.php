<?php
require_once '../connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

$staff_id = $_SESSION['staff_id'];

// Fetch staff data
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM staff WHERE staff_id = ?");
if (!$stmt) {
    die("SQL Error: Please contact the administrator.");
}
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Success/Error message handling
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
    <title>Staff Profile</title>
    <link rel="stylesheet" href="../CSS/ProfileStaff.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo-and-title">
            <div class="logo-circle">
                <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
            </div>
            <h1><a href="staff_dashboard.php">Staff Panel</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="staff_dashboard.php">Dashboard</a></li>
                <li><a href="staff_managecustomer.php">Manage Customer</a></li>
                <li><a href="staff_manageorder.php">Manage Order</a></li>
                <li><a href="staff_menu.php">Manage Menu</a></li>
                <li><a href="staff_viewfeedback.php">View Feedback</a></li>
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
          <i class="fa-solid fa-user-tie profile-icon"></i>
          <h2><?php echo htmlspecialchars($staff['name']); ?></h2>
          <p>Staff Profile</p>
        </div>

        <form action="profStaffUpdate.php" method="POST" id="profile-form">
          <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" disabled>
          </div>

          <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" readonly class="readonly-field">
          </div>

          <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($staff['phone_no'] ?? ''); ?>" disabled>
          </div>

          <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($staff['address'] ?? ''); ?>" disabled>
          </div>

          <div class="profile-actions">
            <button type="button" id="edit-btn" class="btn">Edit Profile</button>
            <button type="submit" id="save-btn" class="btn" name="update_profile" style="display:none;">Save Changes</button>
            <button type="button" id="cancel-btn" class="btn" style="display:none;">Cancel</button>
            <a href="../staff/change_staff.php" class="btn">Change Password</a><br><br>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<script>
  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const inputs = document.querySelectorAll("#profile-form input");

  let originalValues = {};
  inputs.forEach(input => {
    originalValues[input.name] = input.value;
  });

  // Enable edit mode (email remains readonly)
  editBtn.addEventListener("click", () => {
    inputs.forEach(input => {
      if (input.name !== "" && input.name !== "email") input.disabled = false;
    });
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
  });

  // Cancel edit mode
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

</body>
</html>
