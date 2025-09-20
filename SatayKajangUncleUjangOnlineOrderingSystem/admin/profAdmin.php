<?php
require_once '../connect.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;

// Ambil data admin dari database
$stmt = $conn->prepare("SELECT admin_name, email, phone_no, address FROM admin WHERE admin_id = ?");

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

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
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/profAdmin.css">
    <link rel="stylesheet" href="../CSS/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo-and-title">
            <div class="logo-circle">
                <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
            </div>
            <h1><a href="admin_dashboard.php">Admin Panel</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admincustomer.php">Manage Customer</a></li>
                <li><a href="adminstaff.php">Manage Staff</a></li>
                <li><a href="manageadmin.php" class="active">Manage Admin</a></li>
                <li><a href="admin_menu.php">View Menu</a></li>
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
          <i class="fa-solid fa-user-shield profile-icon"></i>
          <h2><?php echo htmlspecialchars($admin['admin_name']); ?></h2>
          <p>Admin Profile</p>
        </div>

        <form action="profAdminUpdate.php" method="POST" id="profile-form">
  <div class="form-group">
    <label>Username:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($admin['admin_name']); ?>" disabled>
  </div>

  <div class="form-group">
    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" disabled readonly class="readonly-field">
  </div>


  <div class="form-group">
       <label>Phone Number:</label>
       <input type="text" name="phone_no" value="<?php echo htmlspecialchars($admin['phone_no'] ?? ''); ?>" disabled>
  </div>

  <div class="form-group">
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($admin['address'] ?? ''); ?>" disabled>
  </div>

  <div class="profile-actions">
    <button type="button" id="edit-btn" class="btn">Edit Profile</button>
    <button type="submit" id="save-btn" class="btn" name="update_profile" style="display:none;">Save Changes</button>
    <button type="button" id="cancel-btn" class="btn" style="display:none;">Cancel</button>
    <a href="../admin/change_admin.php" class="btn">Change Password</a><br><br>
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

  editBtn.addEventListener("click", () => {
    inputs.forEach(input => {
      if (input.name !== "") input.disabled = false;
    });
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
  });

  cancelBtn.addEventListener("click", () => {
    inputs.forEach(input => {
      input.value = originalValues[input.name];
      input.disabled = true;
    });
    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";
  });

  editBtn.addEventListener("click", () => {
  inputs.forEach(input => {
    if (input.name !== "" && input.name !== "email") input.disabled = false;
  });
  editBtn.style.display = "none";
  saveBtn.style.display = "inline-block";
  cancelBtn.style.display = "inline-block";
});

</script>

</body>
</html>
