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
$stmt = $conn->prepare("SELECT name, email, phone_no, address, staff_image FROM staff WHERE staff_id = ?");
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

    <link rel="stylesheet" href="../CSS/profCust.css">
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    
</head>
<body>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header" id="staffDropdown">
            <img src="../image/LogoSataysebenarReal.png" alt="Logo">
            <h2>Staff Panel <i class="fa-solid fa-caret-down"></i></h2>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profStaff.php"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="staff_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="staff_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="staff_managecustomer.php" class="active"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
            <li><a href="staff_menu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a></li>
            <li><a href="staff_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>

<main>
  <section class="profile-wrapper">
    <div class="profile-container">
      <!-- SATU form sahaja -->
      <form action="upload_img_staff.php" method="POST" enctype="multipart/form-data" id="profile-form">
        
        <?php if (isset($success_message)): ?>
            <div class="message-box success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message-box error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="profile-card">
          <div class="profile-header">
            <div class="profile-image-container">
              <?php if (!empty($staff['staff_image'])): ?>
                  <img src="../uploads/<?php echo htmlspecialchars($staff['staff_image']); ?>" alt="Profile Image" class="profile-image">

              <?php else: ?>
                  <img src="../image/default-avatar.png" alt="Default Avatar" class="staff-image">
              <?php endif; ?>
            </div>

            <h2><?php echo htmlspecialchars($staff['name']); ?></h2>
            <p>Customer Profile</p>
          </div>

          <!-- Upload gambar -->
          <div class="form-group">
            <label>Profile Image:</label>
                 <input type="file" name="staff_image" accept="image/*">
                    <button type="submit">Upload</button>
          </div>

        <form action="profStaffUpdate.php" method="POST" id="profile-form">
          <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($staff['name']); ?>" required disabled>
          </div>

          <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($staff['email']); ?>" readonly class="readonly-field">
          </div>

          <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($staff['phone_no'] ?? ''); ?>" required disabled>
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

  // Extra validation for phone number length
const form = document.getElementById("profile-form");

form.addEventListener("submit", function(event) {
  const phoneInput = form.querySelector("input[name='phone_no']");
  const phoneValue = phoneInput.value.trim();

  if (phoneValue.length < 11 || phoneValue.length > 12 || !/^\d+$/.test(phoneValue)) {
    alert("Phone number must be 11 to 12 digits only.");
    event.preventDefault(); // stop form submit
  }
});

document.getElementById("staffDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});

</script>

</body>
</html>
