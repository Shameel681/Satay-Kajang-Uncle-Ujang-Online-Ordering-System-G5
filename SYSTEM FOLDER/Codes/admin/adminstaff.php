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
$show_add_form = false;

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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_staff'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone_no']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($phone_no) || empty($password) || empty($address)) {
        $message = "All fields are required.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
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

        // Check if email already exists
        $check_sql = "SELECT staff_id FROM staff WHERE email=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email is already registered for another staff.";
            $message_type = "error";
        } else {
            // Generate next staff_id
            $id_sql = "SELECT MAX(staff_id) AS max_id FROM staff";
            $result = $conn->query($id_sql);
            $row = $result->fetch_assoc();
            $staff_id = ($row['max_id'] ?? 100) + 1;

            // Insert staff
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
                    $mail->Username = 'toonpow43@gmail.com';
                    $mail->Password = 'mzyp uzsq aarf mmmq';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('youremail@gmail.com', 'Satay Kajang Admin');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Staff Account Created';
                    $mail->Body = "
                        <h3>Welcome $name,</h3>
                        <p>Your staff account has been created by admin.</p>
                        <p><strong>Staff ID:</strong> $staff_id</p>
                        <p><strong>Temporary Password:</strong> $password</p>
                        <p>Please login at: <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/staff/staff_login.php'>Staff Login</a></p>
                        <p>After login, you may change your password in profile settings.</p>
                        <hr>
                        <p>Best regards,<br>Satay Kajang Uncle Ujang Admin</p>
                    ";

                    $mail->send();
                    $message = "Staff added successfully! Verification email sent to " . $email;
                    $message_type = "success";
                } catch (Exception $e) {
                    $message = "Staff added successfully but email could not be sent. Error: {$mail->ErrorInfo}";
                    $message_type = "success";
                }
                $show_add_form = false;
            } else {
                $message = "Failed to add staff. Please try again.";
                $message_type = "error";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

// Toggle add form
if (isset($_GET['show_form'])) {
    $show_add_form = true;
}

// Fetch staff list
$staff_list = $conn->query("SELECT staff_id, name, email, phone_no, address, created_at, last_logged_in 
                            FROM staff ORDER BY staff_id ASC");
$staff_count = $staff_list ? $staff_list->num_rows : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Manage Staff - Satay Kajang Uncle Ujang</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
          urls: ["../assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="admin_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Uncle Ujang" class="navbar-brand" height="30" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item">
                            <a href="admin_dashboard.php">
                                <i class="fa-solid fa-gauge"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admincustomer.php">
                                <i class="fa-solid fa-users"></i>
                                <p>Manage Customer</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="adminstaff.php">
                                <i class="fa-solid fa-user-tie"></i>
                                <p>Manage Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_menu.php">
                                <i class="fa-solid fa-utensils"></i>
                                <p>View Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_manageorder.php">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <p>Manage Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_viewsale.php">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <p>View Sales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profAdmin.php">
                                <i class="fa-solid fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_logout.php">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="admin_dashboard.php" class="logo">
                            <img src="../image/LogoSataysebenarReal.png" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                </div>
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Search staff..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profAdmin.php">
                                            <i class="fa-solid fa-user me-2"></i> Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="admin_logout.php">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="container">
                <div class="page-inner">
                    <!-- Header -->
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">Manage Staff</h1>
                            <p class="op-7 mb-2">Add, view and manage restaurant staff accounts</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <button class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                <i class="fa-solid fa-user-plus me-2"></i>Add New Staff
                            </button>
                            <span class="badge badge-info fs-6 ms-2"><?= $staff_count ?> Active Staff</span>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Staff Table -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row">
                                <h4 class="card-title">Staff Directory</h4>
                                <div class="card-tools">
                                    <a href="#" class="btn btn-icon btn-link btn-primary btn-xs">
                                        <i class="fa fa-sync-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Staff Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Joined</th>
                                            <th scope="col">Last Login</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($staff_list && $staff_list->num_rows > 0): ?>
                                        <?php $staff_list->data_seek(0); // Reset pointer
                                        while ($staff = $staff_list->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary">S<?= sprintf('%04d', $staff['staff_id']) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-title rounded-circle bg-info text-white">
                                                            <?= strtoupper(substr($staff['name'], 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <strong><?= htmlspecialchars($staff['name']) ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= htmlspecialchars($staff['email']) ?></small>
                                            </td>
                                            <td>
                                                <i class="fa-solid fa-phone text-success me-1"></i>
                                                <?= htmlspecialchars($staff['phone_no']) ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= htmlspecialchars(substr($staff['address'], 0, 25)) ?><?= strlen($staff['address']) > 25 ? '...' : '' ?></small>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($staff['created_at'])) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($staff['last_logged_in']): ?>
                                                    <small class="text-success"><?= date('M d, Y H:i', strtotime($staff['last_logged_in'])) ?></small>
                                                <?php else: ?>
                                                    <small class="text-muted">Never</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="editstaff_admin.php?id=<?= $staff['staff_id'] ?>" class="btn btn-sm btn-warning" title="Edit Staff">
                                                        <i class="fa-solid fa-edit"></i>
                                                    </a>
                                                    <a href="?delete_id=<?= $staff['staff_id'] ?>" class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($staff['name']) ?>?')" title="Delete Staff">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fa-solid fa-user-tie fa-3x mb-3 d-block"></i>
                                                    <h5>No staff members found</h5>
                                                    <p class="mb-0">Add your first staff member to get started.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Staff Modal -->
            <div class="modal fade" id="addStaffModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fa-solid fa-user-plus me-2 text-primary"></i>
                                    Add New Staff Member
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" placeholder="staff@example.com" required>
                                        <div class="form-text">Verification email will be sent to this address</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="phone_no" class="form-control" pattern="[0-9]{10,11}" title="10-11 digits only" placeholder="0123456789" required>
                                        <div class="form-text">Must be 10 or 11 digits</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Temporary Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" minlength="8" placeholder="Minimum 8 characters" required>
                                        <div class="form-text">Staff will be able to change this after first login</div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea name="address" class="form-control" rows="2" placeholder="Enter full address" required></textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="add_staff" value="1">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-user-plus me-2"></i>Add Staff & Send Email
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright">
                        © 2025 Satay Kajang Uncle Ujang. All rights reserved.
                    </div>
                </footer>
            </div>
        </div>

        <!-- Core JS Files -->
        <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>
        <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
        <script src="../assets/js/kaiadmin.min.js"></script>

        <script>
            // Auto-dismiss alerts
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Initialize tooltips if needed
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>