<?php
// profAdmin.php
require_once '../connect.php';
session_start();

// Pastikan hanya admin boleh access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: profAdmin.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch admin info dari database
$sql = "SELECT name, email, phone, created_at FROM users WHERE user_id = ? AND role = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="adminProfile.css">
</head>
<body>

<div class="profile-container">
    <div class="profile-card">
        <h2>👨‍💼 Admin Profile</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($admin['phone']); ?></p>
            <p><strong>Account Created:</strong> <?php echo date('d M Y', strtotime($admin['created_at'])); ?></p>
        </div>

        <div class="profile-actions">
            <a href="editAdminProfile.php" class="btn edit-btn">Edit Profile</a>
            <a href="adminDashboard.php" class="btn dashboard-btn">Back to Dashboard</a>
            <a href="logout.php" class="btn logout-btn">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
