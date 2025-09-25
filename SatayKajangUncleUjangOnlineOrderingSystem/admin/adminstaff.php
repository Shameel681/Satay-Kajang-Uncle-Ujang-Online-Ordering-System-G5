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

// ========== DELETE STAFF ==========
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM staff WHERE staff_id=?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Staff deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Failed to delete staff.";
        $message_type = "error";
    }
    $stmt->close();
}

// ========== ADD STAFF ==========
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone_no'] ?? '';
    $password = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($name) || empty($email) || empty($phone_no) || empty($password) || empty($address)) {
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

        // Cek email staff exist
        $check_sql = "SELECT staff_id FROM staff WHERE email=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email is already registered for another staff.";
            $message_type = "error";
        } else {
            // Generate staff_id terakhir +1
            $id_sql = "SELECT MAX(staff_id) AS max_id FROM staff";
            $result = $conn->query($id_sql);
            $row = $result->fetch_assoc();
            $staff_id = ($row['max_id'] ?? 100) + 1;

            // Insert staff (include phone_no & address)
            $insert_sql = "INSERT INTO staff (staff_id, name, email, phone_no, address, password, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("isssss", $staff_id, $name, $email, $phone_no, $address, $password_hash);

            if ($stmt->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'toonpow43@gmail.com'; // ganti dengan email admin
                    $mail->Password = 'mzyp uzsq aarf mmmq';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('youremail@gmail.com', 'Satay Kajang Admin');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Staff Account Verification';
                    $mail->Body = "
                        <h3>Hi $name,</h3>
                        <p>Your staff account has been created by admin.</p>
                        <p>Staff ID: <b>$staff_id</b></p>
                        <p>Password: <b>$password</b></p>
                        <p>Please login at: <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/staff/staff_login.php'>Staff Login</a></p>
                        <p>After login, you may change your password.</p>
                    ";

                    $mail->send();
                    $message = "Staff added successfully! Verification email sent.";
                    $message_type = "success";
                } catch (Exception $e) {
                    $message = "Staff added but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $message_type = "error";
                }
            } else {
                $message = "Failed to add staff. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }
        $check_stmt->close();
    }
}

// ========== FETCH STAFF ==========
$staff_list = $conn->query("SELECT staff_id, name, email, phone_no, address, created_at, last_logged_in 
                            FROM staff ORDER BY staff_id ASC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Staff - Add Staff</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../CSS/admincustomer.css">
<link rel="stylesheet" href="../CSS/adminstaff.css">
<link rel="stylesheet" href="../CSS/admin_menu.css">
<link rel="stylesheet" href="../CSS/admin_dashboard.css">
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
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="admincustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
            <li><a href="adminstaff.php" class="active"><i class="fa-solid fa-utensils"></i> Manage Staff</a></li>
                    <li><a href="admin_menu.php"><i class="fa-solid fa-utensils"></i> View Menu</a></li>
            <li><a href="admin_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>
<main>

<section class="view-staff mt-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Staff</h2>
            <button id="toggleFormBtn" class="btn btn-custom-register">Register New Staff</button>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th>
                    <th>Created At</th><th>Last Login</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($staff_list && $staff_list->num_rows > 0): ?>
                    <?php while ($staff = $staff_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $staff['staff_id']; ?></td>
                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['phone_no']); ?></td>
                        <td><?php echo htmlspecialchars($staff['address']); ?></td>
                        <td><?php echo $staff['created_at']; ?></td>
                        <td><?php echo $staff['last_logged_in']; ?></td>
                        <td>
                            <a href="editstaff.php?id=<?php echo $staff['staff_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="adminstaff.php?delete_id=<?php echo $staff['staff_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this staff?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No staff found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="add-staff" id="addStaffForm" style="display: none;">
    <div class="container">
        <h2>Add New Staff</h2>
        <?php if (!empty($message)): ?>
            <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
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
            <button type="submit" class="btn btn-primary">Add Staff</button>
        </form>
    </div>
</section>

<script>
document.getElementById('toggleFormBtn').addEventListener('click', function() {
    var form = document.getElementById('addStaffForm');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
});


document.getElementById("adminDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});

</script>

</body>
</html>