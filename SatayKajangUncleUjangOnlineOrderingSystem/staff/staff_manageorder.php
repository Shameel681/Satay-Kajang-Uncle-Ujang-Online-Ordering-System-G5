<?php
// staff_manageorder.php
require_once '../connect.php';


// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}
$staff_name = htmlspecialchars($_SESSION['staff_name']);

// Initialize error and success messages
$error = "";
$success = "";

// Handle Order Status Update
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['order_status'];
    
    // Validate status
    $valid_statuses = ['Processing', 'Completed', 'Cancelled'];
    if (in_array($new_status, $valid_statuses)) {
        // Sediakan query dan semak jika gagal
        $stmt = $conn->prepare("UPDATE orders SET order_status = ?, updated_at = NOW() WHERE order_id = ?");
        if (!$stmt) {
            $error = "❌ SQL Prepare Failed: " . $conn->error;
        } else {
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                $success = "Order status updated successfully!";
            } else {
                $error = "Failed to update order status: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $error = "Invalid order status.";
    }
}

// Get all orders with customer details
$sql = "SELECT 
            o.order_id,
            o.customer_id,
            o.customer_name,
            o.order_date,
            o.total_amount,
            o.payment_status,
            o.order_status,
            c.name as customer_full_name,
            c.email as customer_email,
            c.phone_no as customer_phone
        FROM orders o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Staff Dashboard</title>
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
    
    <style>
        .order-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
            transform: translateY(-2px);
        }
        .order-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }
        .order-body {
            padding: 20px;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }
        .payment-paid { background-color: #d4edda; color: #155724; }
        .payment-pending { background-color: #fff3cd; color: #856404; }
        .payment-cancelled { background-color: #f8d7da; color: #721c24; }
        .order-processing { background-color: #cce5ff; color: #004085; }
        .order-completed { background-color: #d4edda; color: #155724; }
        .order-cancelled { background-color: #f8d7da; color: #721c24; }
        .item-list {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .order-details-btn {
            background-color: #17a2b8;
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        .order-details-btn:hover {
            background-color: #138496;
            color: white;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="staff_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Uncle Ujang" class="navbar-brand" height="30" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="icon-menu"></i>
                        </button>
                    </div>
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
                        <li class="nav-item active">
                            <a href="staff_manageorder.php">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <p>Manage Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
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
                                <input type="text" placeholder="Search orders..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?php echo $staff_name; ?></span>
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
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">Manage Orders</h1>
                            <p class="op-7 mb-2">View and manage all customer orders</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <span class="badge badge-primary fs-6"><?php echo $result->num_rows; ?> Total Orders</span>
                        </div>
                    </div>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row">
                                <h4 class="card-title">
                                    <i class="fa-solid fa-shopping-cart me-2 text-primary"></i>
                                    Orders List
                                </h4>
                                <div class="card-tools">
                                    <button class="btn btn-icon btn-link btn-primary btn-xs">
                                        <i class="fa fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($order = $result->fetch_assoc()): ?>
                                        <?php
                                        // Get order items for this order
                                        // !!! PERUBAHAN DISINI: m.price_each ditukar kepada m.price berdasarkan ralat SQL !!!
                                        $items_sql = "SELECT oi.*, m.food_name, m.price 
                                                      FROM order_items oi 
                                                      JOIN menu m ON oi.food_id = m.food_id 
                                                      WHERE oi.order_id = ?";
                                        $items_stmt = $conn->prepare($items_sql);
                                        
                                        // Semakan ralat PHP yang telah dibetulkan sebelum ini
                                        $items_result = false; 
                                        
                                        if (!$items_stmt) {
                                            // Papar ralat SQL jika prepare gagal
                                            echo "<div class='alert alert-danger'>Kesalahan SQL Item Pesanan untuk Order #" . $order['order_id'] . ": " . $conn->error . "</div>";
                                            continue; // Langkau paparan pesanan ini jika item tidak dapat diambil
                                        }

                                        $items_stmt->bind_param("i", $order['order_id']); 
                                        $items_stmt->execute();
                                        $items_result = $items_stmt->get_result();
                                        ?>
                                        
                                        <div class="order-card">
                                            <div class="order-header">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="mb-0">
                                                            <i class="fas fa-receipt"></i> Order #<?php echo $order['order_id']; ?>
                                                        </h5>
                                                        <small><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></small>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <span class="status-badge payment-<?php echo strtolower($order['payment_status']); ?>">
                                                            <i class="fas fa-credit-card"></i> <?php echo $order['payment_status']; ?>
                                                        </span>
                                                        <span class="status-badge order-<?php echo strtolower($order['order_status']); ?>">
                                                            <i class="fas fa-clock"></i> <?php echo $order['order_status']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="order-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6><i class="fas fa-user"></i> Customer Information</h6>
                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_full_name'] ?? $order['customer_name']); ?></p>
                                                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></p>
                                                        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone'] ?? 'N/A'); ?></p>
                                                        <p class="mb-1"><strong>Customer ID:</strong> #<?php echo $order['customer_id']; ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6><i class="fas fa-shopping-cart"></i> Order Items</h6>
                                                        <div class="item-list">
                                                            <?php if ($items_result && $items_result->num_rows > 0): ?>
                                                                <?php while ($item = $items_result->fetch_assoc()): 
                                                                    // Guna m.price (sekarang sebagai $item['price']) 
                                                                    // Tukar $item['price_each'] kepada $item['price'] di sini
                                                                    $item_price = $item['price']; 
                                                                    ?>
                                                                    <div class="item-row">
                                                                        <div>
                                                                            <strong><?php echo htmlspecialchars($item['food_name']); ?></strong>
                                                                            <br>
                                                                            <small class="text-muted">RM <?php echo number_format($item_price, 2); ?> each</small>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <span class="badge badge-info">Qty: <?php echo $item['quantity']; ?></span>
                                                                            <br>
                                                                            <strong>RM <?php echo number_format($item_price * $item['quantity'], 2); ?></strong>
                                                                        </div>
                                                                    </div>
                                                                <?php endwhile; ?>
                                                            <?php else: ?>
                                                                <div class="text-muted">Tiada item pesanan ditemui.</div>
                                                            <?php endif; ?>
                                                            <div class="total-row">
                                                                <div class="d-flex justify-content-between">
                                                                    <span>Total Amount:</span>
                                                                    <strong>RM <?php echo number_format($order['total_amount'], 2); ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                            <div class="form-group mb-0">
                                                                <label class="form-label">Update Order Status:</label>
                                                                <div class="input-group">
                                                                    <select name="order_status" class="form-control" required>
                                                                        <option value="Processing" <?php echo ($order['order_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                                                        <option value="Completed" <?php echo ($order['order_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                                        <option value="Cancelled" <?php echo ($order['order_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                        <button type="submit" name="update_order_status" class="btn btn-update">
                                                                            <i class="fas fa-save"></i> Update Status
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-4 text-right">
                                                        <a href="../customer/view_order_stat_cust.php?order_id=<?php echo $order['order_id']; ?>" 
                                                            class="btn order-details-btn" target="_blank">
                                                             <i class="fas fa-eye"></i> View Customer Receipt
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if ($items_stmt) $items_stmt->close(); // Tutup statement hanya jika ia wujud dan bukan false ?>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No orders found</h5>
                                        <p class="text-muted">There are currently no orders to manage.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright">
                        © <?php echo date('Y'); ?> Satay Kajang Uncle Ujang. All rights reserved.
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

        // Confirm status update
        $('form').on('submit', function(e) {
            const status = $(this).find('select[name="order_status"]').val();
            const orderId = $(this).find('input[name="order_id"]').val();
            
            if (status === 'Completed') {
                if (!confirm('Are you sure you want to mark Order #' + orderId + ' as Completed? This will notify the customer.')) {
                    e.preventDefault();
                }
            } else if (status === 'Cancelled') {
                if (!confirm('Are you sure you want to cancel Order #' + orderId + '? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>