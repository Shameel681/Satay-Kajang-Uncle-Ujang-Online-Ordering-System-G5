<?php
require_once '../connect.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

$staff_name = htmlspecialchars($_SESSION['staff_name']);

// Initialize messages
$message = '';
$message_type = '';

// Handle Delete Feedback
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];

    if ($type === 'customer') {
        $stmt = $conn->prepare("DELETE FROM feedback_customer WHERE id=?");
        $stmt->bind_param("i", $id);
        $table_name = 'Customer';
    } elseif ($type === 'guest') {
        $stmt = $conn->prepare("DELETE FROM feedback_guest WHERE id=?");
        $stmt->bind_param("i", $id);
        $table_name = 'Guest';
    }

    if (isset($stmt)) {
        if ($stmt->execute()) {
            $message = "$table_name feedback deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Failed to delete $table_name feedback.";
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Fetch Customer Feedback
$customer_feedback = $conn->query("SELECT * FROM feedback_customer ORDER BY created_at DESC");
$customer_count = $customer_feedback ? $customer_feedback->num_rows : 0;

// Fetch Guest Feedback
$guest_feedback = $conn->query("SELECT * FROM feedback_guest ORDER BY created_at DESC");
$guest_count = $guest_feedback ? $guest_feedback->num_rows : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>View Feedback - Satay Kajang Uncle Ujang</title>
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

    <style>
 
    /* Define sea blue color variables */
    :root {
        /* Primary Sea Blue: A classic, deep blue often associated with the ocean */
        --sea-blue-primary: #0077BE; /* A vibrant, rich blue */
        /* Darker Sea Blue for hover/active states or accents */
        --sea-blue-hover: #005A9C;
        /* White text/icons for good contrast on the dark sea blue */
        --sea-blue-text: #FFFFFF;
    }

    /* Override the main sidebar background color with Sea Blue */
    .sidebar {
        background-color: var(--sea-blue-primary) !important;
    }

    /* Override the sidebar logo/header background color to match */
    .sidebar-logo .logo-header {
        background-color: var(--sea-blue-primary) !important;
    }

    /* Adjust text/icon color for better contrast on the dark background */
    .sidebar .nav .nav-item a p,
    .sidebar .nav .nav-item a i,
    .sidebar-logo a.logo {
        color: var(--sea-blue-text) !important; /* White text/icons */
    }

    /* Style for the active/hover state of menu items */
    .sidebar .nav .nav-item.active > a,
    .sidebar .nav .nav-item:hover > a {
        background: var(--sea-blue-hover) !important; /* Darker blue on hover/active */
        color: var(--sea-blue-text) !important; /* Keep text white */
    }
    
    /* Ensure the main content scrollbar has the primary color */
    .sidebar .sidebar-wrapper.scrollbar-inner > .scroll-content {
        background-color: var(--sea-blue-primary) !important;
    }

    /* Adjust button/toggle colors to white for contrast */
    .sidebar .nav-toggle .btn-toggle,
    .sidebar .topbar-toggler {
        color: var(--sea-blue-text) !important; /* White color for toggle buttons */
    }

        .feedback-card {
            border-left: 4px solid #e67e22;
            transition: all 0.3s ease;
        }
        .feedback-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .feedback-rating {
            color: #f39c12;
        }
        .feedback-time {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .tab-content .table {
            background: transparent;
        }
        .nav-tabs .nav-link.active {
            background-color: #e67e22;
            border-color: #e67e22;
            color: white;
        }
        .nav-tabs .nav-link {
            color: #e67e22;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="staff_dashboard.php" class="logo">
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
                            <a href="staff_dashboard.php">
                                <i class="fa-solid fa-gauge"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_managecustomer.php">
                                <i class="fa-solid fa-users"></i>
                                <p>Manage Customer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_menu.php">
                                <i class="fa-solid fa-utensils"></i>
                                <p>Manage Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_manageorder.php">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <p>Manage Orders</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="staff_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_viewsale.php">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <p>View Sales</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profStaff.php">
                                <i class="fa-solid fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_logout.php">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <a href="staff_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
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
                                <input type="text" placeholder="Search feedback..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?php echo htmlspecialchars($staff_name); ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profStaff.php">
                                            <i class="fa-solid fa-user me-2"></i> Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="staff_logout.php">
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
                            <h1 class="fw-bold mb-3">
                                <i class="fa-solid fa-comments text-primary me-2"></i>
                                Customer Feedback
                            </h1>
                            <p class="op-7 mb-2">Review and manage feedback from customers and guests</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-primary fs-6">
                                    <i class="fa-solid fa-users me-1"></i><?php echo $customer_count; ?> Customer
                                </span>
                                <span class="badge badge-secondary fs-6">
                                    <i class="fa-solid fa-user me-1"></i><?php echo $guest_count; ?> Guest
                                </span>
                                <span class="badge badge-info fs-6">
                                    Total: <?php echo $customer_count + $guest_count; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Tabs for Customer and Guest Feedback -->
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="feedbackTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="customer-tab" data-bs-toggle="tab" href="#customer-feedback" role="tab">
                                        <i class="fa-solid fa-users me-1"></i>Customer Feedback (<?php echo $customer_count; ?>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="guest-tab" data-bs-toggle="tab" href="#guest-feedback" role="tab">
                                        <i class="fa-solid fa-user me-1"></i>Guest Feedback (<?php echo $guest_count; ?>)
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="feedbackTabContent">
                                <!-- Customer Feedback Tab -->
                                <div class="tab-pane fade show active" id="customer-feedback" role="tabpanel">
                                    <?php if ($customer_count > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ID</th>
                                                        <th>Customer</th>
                                                        <th>Email</th>
                                                        <th>Feedback</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $customer_feedback->data_seek(0);
                                                    $no = 1;
                                                    while ($row = $customer_feedback->fetch_assoc()): 
                                                    ?>
                                                    <tr class="feedback-card">
                                                        <td><?php echo $no++; ?></td>
                                                        <td>
                                                            <span class="badge badge-secondary">CF<?php echo sprintf('%04d', $row['id']); ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-sm me-2">
                                                                    <span class="avatar-title rounded-circle bg-primary">
                                                                        <?php echo strtoupper(substr($row['customer_name'], 0, 1)); ?>
                                                                    </span>
                                                                </div>
                                                                <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted"><?php echo htmlspecialchars($row['customer_email']); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="feedback-content" style="max-height: 100px; overflow-y: auto;">
                                                                <?php echo nl2br(htmlspecialchars(substr($row['feedback'], 0, 150))); ?>
                                                                <?php echo strlen($row['feedback']) > 150 ? '...' : ''; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small class="feedback-time"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <a href="?delete=<?php echo $row['id']; ?>&type=customer" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Delete this customer feedback?')"
                                                               title="Delete Feedback">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fa-solid fa-comments fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No customer feedback yet</h5>
                                            <p class="text-muted">Customer feedback will appear here when submitted.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Guest Feedback Tab -->
                                <div class="tab-pane fade" id="guest-feedback" role="tabpanel">
                                    <?php if ($guest_count > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>ID</th>
                                                        <th>Guest</th>
                                                        <th>Email</th>
                                                        <th>Feedback</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $guest_feedback->data_seek(0);
                                                    $no = 1;
                                                    while ($row = $guest_feedback->fetch_assoc()): 
                                                    ?>
                                                    <tr class="feedback-card">
                                                        <td><?php echo $no++; ?></td>
                                                        <td>
                                                            <span class="badge badge-secondary">GF<?php echo sprintf('%04d', $row['id']); ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-sm me-2">
                                                                    <span class="avatar-title rounded-circle bg-secondary">
                                                                        <?php echo strtoupper(substr($row['guest_name'], 0, 1)); ?>
                                                                    </span>
                                                                </div>
                                                                <strong><?php echo htmlspecialchars($row['guest_name']); ?></strong>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted"><?php echo htmlspecialchars($row['guest_email']); ?></small>
                                                        </td>
                                                        <td>
                                                            <div class="feedback-content" style="max-height: 100px; overflow-y: auto;">
                                                                <?php echo nl2br(htmlspecialchars(substr($row['feedback'], 0, 150))); ?>
                                                                <?php echo strlen($row['feedback']) > 150 ? '...' : ''; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small class="feedback-time"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></small>
                                                        </td>
                                                        <td>
                                                            <a href="?delete=<?php echo $row['id']; ?>&type=guest" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Delete this guest feedback?')"
                                                               title="Delete Feedback">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="fa-solid fa-user fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No guest feedback yet</h5>
                                            <p class="text-muted">Guest feedback will appear here when submitted.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fa-solid fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Customer Feedback</p>
                                                <h4 class="card-title"><?php echo $customer_count; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Guest Feedback</p>
                                                <h4 class="card-title"><?php echo $guest_count; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fa-solid fa-comments"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Feedback</p>
                                                <h4 class="card-title"><?php echo $customer_count + $guest_count; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <!-- Footer -->
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

            // Make feedback expandable
            document.querySelectorAll('.feedback-content').forEach(content => {
                if (content.scrollHeight > content.clientHeight) {
                    content.title = 'Click to expand';
                    content.style.cursor = 'pointer';
                    content.addEventListener('click', function() {
                        this.style.maxHeight = this.style.maxHeight ? null : 'none';
                    });
                }
            });

            // Tab switching functionality
            document.querySelectorAll('#feedbackTabs .nav-link').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (e) {
                    const target = e.target.getAttribute('href');
                    const table = document.querySelector(target + ' .table');
                    if (table) {
                        table.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        </script>
</body>
</html>