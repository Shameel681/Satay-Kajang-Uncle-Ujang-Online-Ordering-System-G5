<?php
require_once '../connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin data
$stmt = $conn->prepare("SELECT admin_name, email, phone_no, address, profile_image FROM admin WHERE admin_id = ?");
if (!$stmt) {
    die("SQL Error: Please contact the administrator.");
}
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    die("Admin profile not found.");
}

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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../CSS/ProfileAdmin.css">
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
    <link rel="stylesheet" href="../CSS/profCust.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="dashboard-wrapper">

     <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header" id="adminDropdown">
            <img src="../image/LogoSataysebenarReal.png" alt="Logo">
            <h2>Admin Panel <i class="fa-solid fa-caret-down"></i></h2>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profAdmin.php"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="admincustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
               <li><a href="adminstaff.php"><i class="fa-solid fa-utensils"></i> Manage Staff</a></li>
                       <li><a href="admin_menu.php"><i class="fa-solid fa-utensils"></i> View Menu</a></li>
            <li><a href="admin_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>

<main>
  <section class="profile">
    <div class="profile-container">
      <!-- SATU form sahaja -->
      <form action="upload_img_admin.php" method="POST" enctype="multipart/form-data" id="profile-form">
        
        <?php if (isset($success_message)): ?>
            <div class="message-box success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message-box error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="profile-card">
          <div class="profile-header">
            <div class="profile-image-container">
              <?php if (!empty($admin['profile_image'])): ?>
                  <img src="../uploads/admin/<?php echo htmlspecialchars($admin['profile_image']); ?>" 
                       alt="Profile Image" class="profile-image">
              <?php else: ?>
                  <img src="../image/default-avatar.png" alt="Default Avatar" class="profile-image">
              <?php endif; ?>
            </div>

            <h2><?php echo htmlspecialchars($admin['admin_name']); ?></h2>
            <p>Admin Profile</p>
          </div>

          <!-- Upload gambar -->
          <div class="form-group">
            <label>Profile Image:</label>
                 <input type="file" name="profile_image" accept="image/*" required>
                    <button type="submit">Upload</button>
          </div>
     

          <!-- Update info -->
          <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="admin_name" value="<?php echo htmlspecialchars($admin['admin_name']); ?>" disabled>
          </div>

          <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" readonly class="readonly-field">
          </div>

          <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_no" value="<?php echo htmlspecialchars($admin['phone_no'] ?? ''); ?>" disabled>
          </div>

          <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($admin['address'] ?? ''); ?>" disabled>
          </div>

          <!-- Button -->
          <div class="profile-actions">
            <button type="button" id="edit-btn" class="btn">Edit Profile</button>
            <button type="submit" id="save-btn" class="btn" name="update_profile" style="display:none;">Save Changes</button>
            <button type="button" id="cancel-btn" class="btn" style="display:none;">Cancel</button>
            <a href="../admin/change_admin.php" class="btn">Change Password</a><br><br>
          </div>
        </div>
      </form>
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

  // Enable edit mode (email stays readonly)
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
 
document.getElementById("adminDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});

</script>

</body>
</html>
