<?php
// admin_dashboard.php - Adapted from staff_dashboard.php for Admin use

// Start session for login check and data retrieval
session_start();
// NOTE: We assume '../connect.php' establishes a $conn using MySQLi, e.g., $conn = new mysqli(...)
require_once '../connect.php'; 

// Check session admin (CHANGE: Updated from 'staff_loggedin' to 'admin_loggedin')
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
// CHANGE: Updated from 'staff_id' to 'admin_id'
$admin_id = $is_loggedin ? $_SESSION['admin_id'] : null;
// CHANGE: Updated from 'staff_name' to 'admin_name'
$admin_name = $is_loggedin ? htmlspecialchars($_SESSION['admin_name']) : '';

if (!$is_loggedin) {
    // CHANGE: Updated redirection link to the admin login page
    header("Location: profAdmin.php");
    exit;
}

// =========================================================================
// 1. MySQLi Data Fetching Helper Function 🛠️
//    This replaces the error-prone PDO fetch methods with a safe MySQLi wrapper.
// =========================================================================

/**
 * Executes a MySQLi prepared statement and returns the value of the first column.
 * Used for COUNT, SUM, and other single-result queries.
 * @param mysqli $conn The database connection object.
 * @param string $query The SQL query with '?' placeholders.
 * @param array $params Array of parameters to bind.
 * @param string $types String of types for parameters ('s', 'i', 'd', 'b').
 * @return mixed The single value result or null on failure/no result.
 */
function mysqli_fetch_single_value($conn, $query, $params = [], $types = null) {
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("MySQLi Prepare Error: " . $conn->error);
        return null;
    }

    if (!empty($params)) {
        // Default to 's' (string) if types are not provided for simplicity
        if ($types === null) {
            $types = str_repeat('s', count($params)); 
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    // Return the value of the first column
    return $data ? reset($data) : null;
}


// =========================================================================
// 2. DATA FETCHING LOGIC (Using MySQLi Helper) 📊
// =========================================================================

try {
    // --- A. Quick Stats Cards ---
    
    // NOTE: This assumes the table name 'customer' is correct.
    $total_customers = (int)mysqli_fetch_single_value($conn, "SELECT COUNT(customer_id) FROM customer");

    // Total Orders (All time)
    $total_orders = (int)mysqli_fetch_single_value($conn, "SELECT COUNT(order_id) FROM orders");

    // Total Sales Revenue
    $total_sales_raw = mysqli_fetch_single_value($conn, "SELECT SUM(total_amount) FROM orders WHERE order_status = 'Completed'");
    $total_sales = number_format($total_sales_raw ?? 0, 2); 

    // --- B. Performance Cards (Today's Data) ---

    $today = date("Y-m-d");

    // Today's Sales
    $todays_sales_raw = mysqli_fetch_single_value($conn, 
        "SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = ? AND order_status = 'Completed'", 
        [$today], 's'
    );
    $todays_sales = number_format($todays_sales_raw ?? 0, 2);

    // New Orders Today
    $new_orders = (int)mysqli_fetch_single_value($conn, 
        "SELECT COUNT(order_id) FROM orders WHERE DATE(order_date) = ?", 
        [$today], 's'
    );

    // New Customers Today (Assuming 'created_at' column exists)
    // NOTE: This assumes the table name 'customer' is correct.
    $new_customers = (int)mysqli_fetch_single_value($conn, 
        "SELECT COUNT(customer_id) FROM customer WHERE DATE(created_at) = ?", 
        [$today], 's'
    );

    // --- C. Recent Orders Table (Fetching multiple rows) ---
    $recent_orders = [];
    $stmt = $conn->prepare("
        SELECT o.order_id, o.customer_name, o.total_amount, o.order_status
        FROM orders o ORDER BY o.order_date DESC LIMIT 5
    ");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // --- D. Top Menu Items (Fetching multiple rows) ---
    $top_menu_items = [];
    $stmt = $conn->prepare("
        SELECT m.food_name, SUM(oi.quantity) AS total_quantity_ordered
        FROM order_items oi
        JOIN menu m ON oi.food_id = m.food_id
        GROUP BY m.food_name
        ORDER BY total_quantity_ordered DESC
        LIMIT 3
    ");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $top_menu_items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // --- E. Orders Breakdown (for Doughnut Chart) ---
    $order_status_counts = ['Completed' => 0, 'Pending' => 0, 'Cancelled' => 0];
    $stmt = $conn->prepare("SELECT order_status, COUNT(order_id) as count FROM orders GROUP BY order_status");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $status = $row['order_status'];
            if (isset($order_status_counts[$status])) {
                $order_status_counts[$status] = (int)$row['count'];
            }
        }
        $stmt->close();
    }
    
    // Prepare data for JS chart
    $orders_chart_data_js = json_encode(array_values($order_status_counts));
    $orders_chart_labels_js = json_encode(array_keys($order_status_counts));

    // --- F. Sales Chart (Last 7 Days) ---
    $sales_data = [];
    $labels = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date("Y-m-d", strtotime("-$i days"));
        $day_label = date("D", strtotime("-$i days"));
        $labels[] = $day_label;
        
        $daily_sales = mysqli_fetch_single_value($conn, 
            "SELECT SUM(total_amount) FROM orders WHERE DATE(order_date) = ? AND order_status = 'Completed'", 
            [$date], 's'
        );
        $sales_data[] = (float)($daily_sales ?? 0);
    }
    $sales_chart_labels_js = json_encode($labels);
    $sales_chart_data_js = json_encode($sales_data);

} catch (Exception $e) {
    // Handle database and other errors gracefully
    error_log("Dashboard Data Fetch Error: " . $e->getMessage());
    $error_message = "A system error occurred. Data shown may be incomplete.";
    
    // Set placeholder/zero values to prevent breaking the HTML/JS
    $total_customers = $total_orders = $new_orders = $new_customers = 0;
    $total_sales = $todays_sales = '0.00';
    $recent_orders = [];
    $top_menu_items = [];
    $orders_chart_data_js = json_encode([0, 0, 0]);
    $orders_chart_labels_js = json_encode(['Completed', 'Pending', 'Cancelled']);
    $sales_chart_labels_js = json_encode(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
    $sales_chart_data_js = json_encode([0, 0, 0, 0, 0, 0, 0]);
}

// Close MySQLi connection
if (isset($conn) && is_a($conn, 'mysqli')) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Satay Kajang Uncle Ujang</title>
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function () { sessionStorage.fonts = true; }
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
                        <li class="nav-item active">
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
                    <a href="admin_dashboard.php" class="logo">
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
                                <input type="text" placeholder="Search..." class="form-control" />
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

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">Welcome, <strong><?php echo $admin_name; ?></strong>!</h1>
                            <p class="op-7 mb-2">Manage Satay Kajang Uncle Ujang system with the tools below.</p>
                             <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Error!</strong> <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
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
                                                <p class="card-category">Customers</p>
                                                <h4 class="card-title"><?php echo $total_customers; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fa-solid fa-box"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Orders</p>
                                                <h4 class="card-title"><?php echo $total_orders; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="fa-solid fa-chart-line"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Sales</p>
                                                <h4 class="card-title">RM <?php echo $total_sales; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <h4 class="card-title">Sales Overview (Last 7 Days)</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <h4 class="card-title">Orders Breakdown</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="ordersChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-head-row card-tools-still-right">
                                        <div class="card-title">Recent Orders</div>
                                        <div class="card-tools">
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-clean" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="admin_manageorder.php">View All</a>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Order ID</th>
                                                    <th>Customer</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($recent_orders)): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">No recent orders found.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($recent_orders as $order): ?>
                                                        <tr>
                                                            <td>#<?php echo htmlspecialchars(str_pad($order['order_id'], 5, '0', STR_PAD_LEFT)); ?></td>
                                                            <td><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></td>
                                                            <td>RM <?php echo number_format($order['total_amount'], 2); ?></td>
                                                            <td>
                                                                <?php
                                                                    $badge_class = 'badge-secondary';
                                                                    $status = htmlspecialchars($order['order_status']);
                                                                    if ($status === 'Completed' || $status === 'Paid') {
                                                                        $badge_class = 'badge-success';
                                                                    } elseif ($status === 'Pending' || $status === 'Processing') {
                                                                        $badge_class = 'badge-warning';
                                                                    } elseif ($status === 'Cancelled' || $status === 'Failed') {
                                                                        $badge_class = 'badge-danger';
                                                                    }
                                                                ?>
                                                                <span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Top Menu Items</div>
                                </div>
                                <div class="card-body pb-0">
                                    <?php if (empty($top_menu_items)): ?>
                                        <p class="text-center">No menu items ordered yet.</p>
                                    <?php else: ?>
                                        <?php
                                            $icons = ['fa-drumstick-bite text-primary', 'fa-drumstick-bite text-success', 'fa-utensils text-warning'];
                                            $descriptions = ['Most Ordered', 'Popular Choice', 'Top Item'];
                                            foreach ($top_menu_items as $index => $item):
                                                $icon_class = $icons[$index] ?? 'fa-circle text-info';
                                                $description = $descriptions[$index] ?? 'High Demand';
                                        ?>
                                        <div class="d-flex">
                                            <div class="avatar">
                                                <i class="fa-solid <?php echo $icon_class; ?>" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="flex-1 pt-1 ms-2">
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['food_name']); ?></h6>
                                                <small class="text-muted"><?php echo $description; ?> (<?php echo htmlspecialchars($item['total_quantity_ordered']); ?> units)</small>
                                            </div>
                                            <div class="d-flex ms-auto align-items-center">
                                                <h4 class="text-info fw-bold"><?php echo htmlspecialchars($item['total_quantity_ordered']); ?></h4>
                                            </div>
                                        </div>
                                        <?php if ($index < count($top_menu_items) - 1): ?>
                                            <div class="separator-dashed"></div>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row row-card-no-pd">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6><b>Today's Sales</b></h6>
                                            <p class="text-muted">Daily Revenue (Completed)</p>
                                        </div>
                                        <h4 class="text-success fw-bold">RM <?php echo $todays_sales; ?></h4>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6><b>New Orders</b></h6>
                                            <p class="text-muted">Today's Orders</p>
                                        </div>
                                        <h4 class="text-primary fw-bold"><?php echo $new_orders; ?></h4>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary w-60" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6><b>New Customers</b></h6>
                                            <p class="text-muted">Today's Signups</p>
                                        </div>
                                        <h4 class="text-info fw-bold"><?php echo $new_customers; ?></h4>
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-info w-40" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
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
                </div>
            </footer>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Use PHP data generated above
        const salesLabels = <?php echo $sales_chart_labels_js; ?>;
        const salesData = <?php echo $sales_chart_data_js; ?>;
        const ordersLabels = <?php echo $orders_chart_labels_js; ?>;
        const ordersData = <?php echo $orders_chart_data_js; ?>;

        // Sales Overview Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Sales (RM)',
                    data: salesData,
                    borderColor: '#e67e22',
                    backgroundColor: 'rgba(230, 126, 34, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (RM)'
                        }
                    }
                }
            }
        });

        // Orders Breakdown Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ordersLabels,
                datasets: [{
                    data: ordersData,
                    backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c'], // Green for Completed, Yellow for Pending, Red for Cancelled
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>