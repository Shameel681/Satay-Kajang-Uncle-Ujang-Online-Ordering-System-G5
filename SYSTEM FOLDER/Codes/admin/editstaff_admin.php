<?php
// editstaff_admin.php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminstaff.php");
    exit;
}

$staff_id = intval($_GET['id']);
$error = '';

// Fetch data staff
$sql = "SELECT staff_id, name, email, phone_no, address, created_at FROM staff WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

if (!$staff) {
    $error = "Staff not found!";
}

// Update staff - HANYA phone_no dan address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone_no = trim($_POST['phone_no']);
    $address = trim($_POST['address']);

    // Validation - hanya untuk fields yang boleh edit
    if (empty($phone_no) || empty($address)) {
        $error = "Phone number and address are required.";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
        $error = "Phone number must be 10 or 11 digits.";
    } else {
        $update = "UPDATE staff SET phone_no=?, address=? WHERE staff_id=?";
        $stmt = $conn->prepare($update);
        if ($stmt) {
            $stmt->bind_param("ssi", $phone_no, $address, $staff_id);
            
            if ($stmt->execute()) {
                header("Location: adminstaff.php?message=Staff updated successfully");
                exit;
            } else {
                $error = "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Prepare failed: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - Satay Kajang Uncle Ujang</title>
    
    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function () { sessionStorage.fonts = true; }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    
    <style>
        .readonly-field {
            background-color: #f8f9fa !important;
            color: #6c757d;
            cursor: not-allowed;
        }
        .readonly-field:focus {
            background-color: #f8f9fa !important;
            border-color: #ced4da;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- SIDEBAR - sama seperti adminstaff.php -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="admin_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Uncle Ujang" class="navbar-brand" height="30" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>
                    <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item"><a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i><p>Dashboard</p></a></li>
                        <li class="nav-item"><a href="admincustomer.php"><i class="fa-solid fa-users"></i><p>Manage Customer</p></a></li>
                        <li class="nav-item active"><a href="adminstaff.php"><i class="fa-solid fa-user-tie"></i><p>Manage Staff</p></a></li>
                        <li class="nav-item"><a href="admin_menu.php"><i class="fa-solid fa-utensils"></i><p>View Menu</p></a></li>
                        <li class="nav-item"><a href="admin_viewfeedback.php"><i class="fa-solid fa-comments"></i><p>View Feedback</p></a></li>
                        <li class="nav-item"><a href="profAdmin.php"><i class="fa-solid fa-user"></i><p>Profile</p></a></li>
                        <li class="nav-item"><a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i><p>Logout</p></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <!-- MAIN HEADER -->
            <div class="main-header">
                <div class="main-header-logo">
                    <a href="admin_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
                </div>
                <nav class="navbar topbar navbar-expand">
                    <div class="container-fluid">
                        <div class="d-flex align-items-center">
                            <h4 class="navbar-brand mb-0">Edit Staff</h4>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="container">
                <div class="page-inner">
                    <!-- Page Header -->
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">Edit Staff Information</h1>
                            <p class="op-7 mb-2">Update phone number and address only</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="adminstaff.php" class="btn btn-secondary btn-round">
                                <i class="fa-solid fa-arrow-left me-2"></i>Back to Staff List
                            </a>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($staff): ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row">
                                <h4 class="card-title">
                                    <i class="fa-solid fa-user-edit me-2 text-primary"></i>
                                    Staff Details - S<?= sprintf('%04d', $staff['staff_id']) ?>
                                </h4>
                                <div class="card-tools">
                                    <span class="badge badge-info">
                                        <i class="fa-solid fa-edit me-1"></i>Edit Mode
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <!-- Staff ID (READONLY) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fa-solid fa-id-badge me-1 text-muted"></i>Staff ID
                                        </label>
                                        <input type="text" class="form-control readonly-field" 
                                               value="S<?= sprintf('%04d', $staff['staff_id']) ?>" readonly>
                                        <div class="form-text text-muted">Cannot be changed</div>
                                    </div>
                                    
                                    <!-- Name (READONLY) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <i class="fa-solid fa-user me-1 text-muted"></i>Full Name
                                        </label>
                                        <input type="text" class="form-control readonly-field" 
                                               value="<?= htmlspecialchars($staff['name']) ?>" readonly>
                                        <div class="form-text text-muted">Cannot be changed</div>
                                    </div>
                                    
                                    <!-- Email (READONLY) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fa-solid fa-envelope me-1 text-muted"></i>Email
                                        </label>
                                        <input type="email" class="form-control readonly-field" 
                                               value="<?= htmlspecialchars($staff['email']) ?>" readonly>
                                        <div class="form-text text-muted">Cannot be changed</div>
                                    </div>
                                    
                                    <!-- Joined Date (READONLY) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fa-solid fa-calendar me-1 text-muted"></i>Joined Date
                                        </label>
                                        <input type="text" class="form-control readonly-field" 
                                               value="<?= date('M d, Y', strtotime($staff['created_at'])) ?>" readonly>
                                        <div class="form-text text-muted">Join date</div>
                                    </div>

                                     <!-- Phone Number (EDITABLE) -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold text-primary">
                                            <i class="fa-solid fa-phone me-2 text-success"></i>Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="phone_no" class="form-control" 
                                               pattern="[0-9]{10,11}" title="10-11 digits only"
                                               value="<?= htmlspecialchars($staff['phone_no']) ?>" required>
                                        <div class="form-text">Must be 10 or 11 digits only</div>
                                    </div>
                                    
                                    <!-- Address (EDITABLE) -->
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold text-primary">
                                            <i class="fa-solid fa-map-marker-alt me-2 text-danger"></i>Address <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="address" class="form-control" rows="3" required><?= htmlspecialchars($staff['address']) ?></textarea>
                                        <div class="form-text">Full address of the staff member</div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Only <strong>Phone Number</strong> and <strong>Address</strong> can be edited. 
                                    Other information requires admin privileges to modify.
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <a href="adminstaff.php" class="btn btn-secondary">
                                        <i class="fa-solid fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-2"></i>Update Phone & Address
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright">© 2025 Satay Kajang Uncle Ujang. All rights reserved.</div>
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
                setTimeout(() => bsAlert.close(), 5000);
            });
        }, 100);

        // Phone number input - only numbers
        document.querySelector('input[name="phone_no"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const phone = document.querySelector('input[name="phone_no"]').value;
            if (phone.length < 10 || phone.length > 11) {
                e.preventDefault();
                alert('Phone number must be 10 or 11 digits');
                return false;
            }
        });
    </script>
</body>
</html>