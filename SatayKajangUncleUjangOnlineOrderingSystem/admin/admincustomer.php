<?php
// admin/admincustomer.php
require_once '../connect.php';

// Initialize error message
$error = "";

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: profAdmin.php");
    exit;
}

// Handle Edit Customer
if (isset($_POST['edit_customer'])) {
    $id = $_POST['customer_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone_no']);
    $address = trim($_POST['address']);

    if (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
        $error = "Phone number must be 10 or 11 digits only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE customer SET name=?, email=?, phone_no=?, address=?, updated_at=NOW() WHERE customer_id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $id);
        if ($stmt->execute()) {
            $success = "Customer updated successfully!";
        }
        $stmt->close();
    }
}

// Handle Delete Customer
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Customer deleted successfully!";
    }
    $stmt->close();
    header("Location: admincustomer.php");
    exit;
}

// Fetch all customers
$result = $conn->query("SELECT * FROM customer ORDER BY created_at DESC");
$customer_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Manage Customers - Satay Kajang Uncle Ujang</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
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
                        <li class="nav-item active">
                            <a href="admincustomer.php">
                                <i class="fa-solid fa-users"></i>
                                <p>Manage Customer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="adminstaff.php">
                                <i class="fa-solid fa-utensils"></i>
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
                            <a href="admin_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
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
                                <input type="text" placeholder="Search customers..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="../assets/img/profile.jpg" alt="Admin" class="avatar-img rounded-circle" />
                                    </div>
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
                            <h1 class="fw-bold mb-3">Manage Customers</h1>
                            <p class="op-7 mb-2">View and manage all customer information</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <span class="badge badge-primary fs-6"><?= $customer_count ?> Total Customers</span>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if (isset($error) && !empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success) && !empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i><?= $success ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Customers Table -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row">
                                <h4 class="card-title">Customers List</h4>
                                <div class="card-tools">
                                    <button class="btn btn-icon btn-link btn-primary btn-xs">
                                        <i class="fa fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Address</th>
                                            <th scope="col">Created</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary">#<?= sprintf('%04d', $row['customer_id']) ?></span>
                                            </td>
                                            <td>
                                                <div class="avatar">
                                                    <span class="avatar-title rounded-circle bg-primary"><?= strtoupper(substr($row['name'], 0, 1)) ?></span>
                                                </div>
                                                <div class="ms-2">
                                                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td>
                                                <i class="fa-solid fa-phone text-success me-1"></i>
                                                <?= htmlspecialchars($row['phone_no']) ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= htmlspecialchars(substr($row['address'], 0, 30)) ?><?= strlen($row['address']) > 30 ? '...' : '' ?></small>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= date('M d, Y', strtotime($row['created_at'])) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['customer_id'] ?>" title="Edit">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?= $row['customer_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?= $row['customer_id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                <i class="fa-solid fa-user-edit me-2"></i>
                                                                Edit Customer: <?= htmlspecialchars($row['name']) ?>
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="customer_id" value="<?= $row['customer_id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                                <input type="text" name="phone_no" class="form-control" value="<?= htmlspecialchars($row['phone_no']) ?>" pattern="[0-9]{10,11}" title="Enter 10 or 11 digit phone number" required>
                                                                <div class="form-text">Must be 10 or 11 digits only</div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Address</label>
                                                                <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($row['address']) ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" name="edit_customer" class="btn btn-success">
                                                                <i class="fa-solid fa-save me-2"></i>Save Changes
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                    <?php if ($customer_count == 0): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fa-solid fa-users fa-3x mb-3 d-block"></i>
                                                    <h5>No customers found</h5>
                                                    <p>There are no customers in the system yet.</p>
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
    
    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>