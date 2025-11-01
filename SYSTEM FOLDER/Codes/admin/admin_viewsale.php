<?php
// Set a specific timezone for all date/time operations (e.g., Kuala Lumpur)
date_default_timezone_set('Asia/Kuala_Lumpur');

// admin/admin_viewsale.php
session_start();
require_once '../connect.php';

// 1. Authentication Check
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
$admin_name = $is_loggedin ? htmlspecialchars($_SESSION['admin_name']) : '';
if (!$is_loggedin) {
    header("Location: profAdmin.php");
    exit;
}

// 2. Data Fetch: Select all completed/paid sales using prepared statement
$sql_sales = "SELECT 
                o.order_id, 
                o.customer_id, 
                c.name AS customer_name,
                o.total_amount, 
                o.order_status, 
                o.payment_status,
                o.order_date
              FROM orders o
              LEFT JOIN customer c ON o.customer_id = c.customer_id
              WHERE o.order_status = ?
                AND o.payment_status = ?
              ORDER BY o.order_date DESC";

$sales_data = [];
if ($stmt = $conn->prepare($sql_sales)) {
    $order_status = 'Completed';
    $payment_status = 'Paid';
    $stmt->bind_param("ss", $order_status, $payment_status);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $sales_data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free(); // Free the result set
    } else {
        // Handle execution error
        die("SQL Execution Failed: " . $stmt->error);
    }
    $stmt->close(); // Close the prepared statement
} else {
    // Handle preparation error
    die("SQL Preparation Failed: " . $conn->error);
}

// Calculate totals
$total_sales_count = count($sales_data);
$total_revenue = array_sum(array_column($sales_data, 'total_amount'));

// 3. CSV Export Logic (Must be before any HTML output)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    
    // Headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Output the column headings - Added Payment Status
    $header = ['Order ID', 'Customer ID', 'Customer Name', 'Total Amount (RM)', 'Order Status', 'Payment Status', 'Order Date'];
    fputcsv($output, $header);
    
    // Output the data
    foreach ($sales_data as $row) {
        $row_output = [
            $row['order_id'],
            $row['customer_id'],
            $row['customer_name'] ?? 'Guest (ID: ' . $row['customer_id'] . ')',
            number_format($row['total_amount'], 2),
            $row['order_status'],
            $row['payment_status'],
            $row['order_date']
        ];
        fputcsv($output, $row_output);
    }
    
    fclose($output);
    $conn->close(); // Close connection on exit
    exit;
}

// Close the database connection once all data processing is complete
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin Dashboard - Satay Kajang Uncle Ujang</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

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

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
</head>
<body>
    <div class="wrapper">
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
                        <li class="nav-item">
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
                        <li class="nav-item active">
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
                                </div>
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?php echo $admin_name; ?></span>
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
            
            <div class="content" style="margin-top: 50px;"> 
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                        <div class="page-header">
                            <h4 class="page-title">Sales Report</h4>
                            
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="?export=csv" class="btn btn-primary btn-round">
                                <i class="fa fa-download"></i> Download Report (CSV)
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon"><div class="icon-big text-center icon-success bubble-shadow-small"><i class="fas fa-chart-bar"></i></div></div>
                                        <div class="col col-stats ms-3">
                                            <div class="numbers">
                                                <p class="card-category">Total Successful Sales</p>
                                                <h4 class="card-title"><?= $total_sales_count ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon"><div class="icon-big text-center icon-warning bubble-shadow-small"><i class="fas fa-dollar-sign"></i></div></div>
                                        <div class="col col-stats ms-3">
                                            <div class="numbers">
                                                <p class="card-category">Total Revenue (RM)</p>
                                                <h4 class="card-title">RM <?= number_format($total_revenue, 2) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Completed and Paid Transactions</div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="sales-table" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer Name</th>
                                                    <th>Total Amount (RM)</th>
                                                    <th>Order Status</th>
                                                    <th>Payment Status</th>
                                                    <th>Order Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($total_sales_count > 0): ?>
                                                    <?php foreach ($sales_data as $sale): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($sale['order_id']) ?></td>
                                                            <td><?= htmlspecialchars($sale['customer_name'] ?? 'Guest') ?></td>
                                                            <td>RM <?= number_format($sale['total_amount'], 2) ?></td>
                                                            <td><span class="badge bg-success"><?= htmlspecialchars($sale['order_status']) ?></span></td>
                                                            <td><span class="badge bg-primary"><?= htmlspecialchars($sale['payment_status']) ?></span></td>
                                                            <td><?= htmlspecialchars(date('d M Y, H:i:s', strtotime($sale['order_date']))) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">No completed and paid sales found.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>
</html>