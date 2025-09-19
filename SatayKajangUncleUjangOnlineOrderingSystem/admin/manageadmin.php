<?php
require_once '../connect.php';
require '../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$message_type = '';

// ========== DELETE ADMIN ==========
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Prevent an admin from deleting themselves (optional, but good practice)
    if ($delete_id == $_SESSION['admin_id']) {
        $message = "You cannot delete your own admin account from this page.";
        $message_type = "error";
    } else {
        $delete_sql = "DELETE FROM admin WHERE admin_id=?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $message = "Admin deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Failed to delete admin.";
            $message_type = "error";
        }
        $stmt->close();
    }
}

// ========== ADD ADMIN ==========
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $admin_name = $_POST['admin_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone_no'] ?? '';
    $password = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? ''; // Added address field

    if (empty($admin_name) || empty($email) || empty($phone_no) || empty($password) || empty($address)) {
        $message = "All fields are required.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address (e.g., name@example.com).";
        $message_type = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
        $message_type = "error";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
        $message = "Phone number must be exactly 10 or 11 digits.";
        $message_type = "error";
    } else {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists for another admin
        $check_sql = "SELECT admin_id FROM admin WHERE email=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email is already registered for another admin.";
            $message_type = "error";
        } else {
            // Generate admin_id (assuming auto-increment or a specific scheme)
            // For simplicity, let's auto-increment from the max_id + 1, similar to staff.
            $id_sql = "SELECT MAX(admin_id) AS max_id FROM admin";
            $result = $conn->query($id_sql);
            $row = $result->fetch_assoc();
            $admin_id = ($row['max_id'] ?? 1) + 1; // Start from 1 if no admins exist

            // Generate a verification token (optional, but good for account activation)
            $verify_token = bin2hex(random_bytes(16));

            // Insert admin (include phone_no, address, is_verified, verify_token, created_at)
            $insert_sql = "INSERT INTO admin (admin_id, admin_name, email, phone_no, address, password, is_verified, verify_token, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 0, ?, NOW())"; // is_verified set to 0 by default
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("issssss", $admin_id, $admin_name, $email, $phone_no, $address, $password_hash, $verify_token);

            if ($stmt->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'toonpow43@gmail.com'; // Change to your admin email
                    $mail->Password = 'mzyp uzsq aarf mmmq'; // Change to your app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('youremail@gmail.com', 'Satay Kajang Admin');
                    $mail->addAddress($email, $admin_name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Admin Account Verification';
                    $mail->Body = "
                        <h3>Hi $admin_name,</h3>
                        <p>Your admin account has been created by another admin.</p>
                        <p>Admin ID: <b>$admin_id</b></p>
                        <p>Password: <b>$password</b></p>
                        <p>Please login at: <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/admin/admin_login.php'>Admin Login</a></p>
                        <p>After login, you may change your password.</p>
                        <p>To verify your account, please click the following link: 
                           <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/admin/admin_verify.php?token=$verify_token'>Verify Account</a></p>
                    ";

                    $mail->send();
                    $message = "Admin added successfully! Verification email sent.";
                    $message_type = "success";
                } catch (Exception $e) {
                    $message = "Admin added but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $message_type = "error";
                }
            } else {
                $message = "Failed to add admin. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }
        $check_stmt->close();
    }
}

// ========== FETCH ADMINS ==========
$admin_list = $conn->query("SELECT admin_id, admin_name, email, phone_no, address, created_at, last_login, is_verified
                            FROM admin ORDER BY admin_id ASC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Admins - Admin Panel</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../CSS/manageadmin.css"> 
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
                <li><a href="manageadmin.php" class="active">Manage Admin</a></li> <li><a href="adminmenu.php">Manage Menu</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>

<section class="view-admins mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Administrators</h2>
            <button id="toggleAdminFormBtn" class="btn btn-primary">Register New Admin</button>
        </div>
        <?php if (!empty($message) && ($_SERVER["REQUEST_METHOD"] != "POST" || $message_type == "success")): ?>
            <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Created At</th>
                    <th>Last Login</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($admin_list && $admin_list->num_rows > 0): ?>
                    <?php while ($admin = $admin_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $admin['admin_id']; ?></td>
                        <td><?php echo htmlspecialchars($admin['admin_name']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td><?php echo htmlspecialchars($admin['phone_no']); ?></td>
                        <td><?php echo htmlspecialchars($admin['address']); ?></td>
                        <td><?php echo $admin['created_at']; ?></td>
                        <td><?php echo $admin['last_logged_in'] ?? 'Never'; ?></td>
                        <td><?php echo $admin['is_verified'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <a href="editadmin.php?id=<?php echo $admin['admin_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="manageadmin.php?delete_id=<?php echo $admin['admin_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this admin?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">No administrators found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="add-admin" id="addAdminForm" style="display: none;">
    <div class="container">
        <h2>Add New Administrator</h2>
        <?php if (!empty($message) && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type == "error"): ?>
            <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Admin Name:</label>
                <input type="text" name="admin_name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_no" pattern="[0-9]{10,11}" title="Phone number must be 10-11 digits" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" minlength="8" required>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Admin</button>
        </form>
    </div>
</section>

<script>
document.getElementById('toggleAdminFormBtn').addEventListener('click', function() {
    var form = document.getElementById('addAdminForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
});

// Show the form again if there was an error after submission
<?php if (!empty($message) && $_SERVER["REQUEST_METHOD"] == "POST" && $message_type == "error"): ?>
    document.getElementById('addAdminForm').style.display = 'block';
<?php endif; ?>
</script>

</body>
</html>