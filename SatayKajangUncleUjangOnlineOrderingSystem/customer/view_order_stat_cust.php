<?php
// Mula sesi dan sambungkan ke database
session_start();
require_once '../connect.php';

// Pastikan pengguna log masuk, jika tidak, redirect
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$order_id = null;

// Ambil maklumat pelanggan untuk header
$customer_name = "Guest";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt_customer = $conn->prepare("SELECT name FROM customer WHERE customer_id = ?");
if ($stmt_customer) {
    $stmt_customer->bind_param("i", $customer_id);
    $stmt_customer->execute();
    $result = $stmt_customer->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt_customer->close();

    if ($customer_data) {
        $customer_name = htmlspecialchars($customer_data['name']);
        $_SESSION['customer_name'] = $customer_name;
    }
}


// 1. Tentukan Order ID mana yang hendak dipaparkan:
if (isset($_GET['order_id'])) {
    // Jika ID dihantar melalui URL (untuk sejarah pesanan)
    $order_id = intval($_GET['order_id']);
} else {
    // Jika tiada ID dihantar (selepas redirect pembayaran), cari ID pesanan terakhir
    $sql_last = "SELECT order_id FROM orders WHERE customer_id = ? ORDER BY order_date DESC LIMIT 1";
    $stmt_last = $conn->prepare($sql_last);
    $stmt_last->bind_param("i", $customer_id);
    $stmt_last->execute();
    $result_last = $stmt_last->get_result();
    
    if ($result_last->num_rows > 0) {
        $last_order = $result_last->fetch_assoc();
        $order_id = $last_order['order_id'];
    }
    $stmt_last->close();
}

// Jika tiada Order ID ditemui (user tiada pesanan), redirect ke menu
if (!$order_id) {
    echo "<script>alert('You have no orders yet. Returning to menu.'); window.location.href='menu.php';</script>";
    exit;
}

// 2. Ambil Maklumat Pesanan, Pelanggan, Bill Code dan Transaction ID
$sql = "SELECT 
            o.*, 
            c.name AS customer_full_name,
            c.email AS customer_email, 
            c.phone_no AS customer_phone 
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ? AND o.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<script>alert('Order not found.'); window.location.href='menu.php';</script>";
    exit;
}

// 3. Ambil Item Pesanan
$sql_items = "SELECT oi.*, m.food_name FROM order_items oi JOIN menu m ON oi.food_id = m.food_id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
$stmt_items->close();

// Tentukan butang aksi
$action_button = ($order['payment_status'] === 'Paid') ? 
    '<button onclick="window.print()" class="btn btn-success float-end">⬇️ Download/Print Receipt</button>' :
    // PENTING: Anda mungkin perlu menghantar ke process_payment.php semula dengan Order ID
    '<a href="payment.php?order_id=' . $order_id . '" class="btn btn-primary float-end">Pay Now</a>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Status #<?php echo $order['order_id']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<style>
    /* Custom Styling for the Receipt Box, menggunakan warna branding anda */
    .receipt-box { 
        background: #fff; 
        max-width: 750px; 
        margin: 50px auto; 
        padding: 40px; 
        border-radius: 15px; 
        box-shadow: 0 8px 30px rgba(0,0,0,0.15); 
        border-top: 5px solid #FEA116; /* Warna branding */
    }
    .receipt-box h2 {
        color: #FEA116; 
        font-family: 'Pacifico', cursive;
        margin-bottom: 20px;
        text-align: center;
    }
    .receipt-box h4 {
        border-bottom: 2px solid #FEA116;
        padding-bottom: 5px;
        margin-top: 25px;
        margin-bottom: 15px;
        color: #000;
    }
    .list-group-item {
        border: none;
        border-bottom: 1px dashed #ddd;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    /* Untuk Print/Download */
    @media print {
        .no-print { display: none; }
        .receipt-box { box-shadow: none; border: 1px solid #000; }
    }
</style>

<body>
<div class="container-xxl bg-white p-0">
    <div class="container-xxl position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
            <a href="../index.php" class="navbar-brand p-0">
                <h1 class="text-primary m-0">
                    <img src="../image/LogoSataysebenarReal.png" alt="Logo" style="width:50px;height:50px;border-radius:50%;margin-right:10px;">
                    Satay Kajang Uncle Ujang
                </h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0 pe-4">
                    <a href="../index.php" class="nav-item nav-link">Home</a>
                    <a href="menu.php" class="nav-item nav-link">Menu</a>
                    <a href="view_order_stat_cust.php" class="nav-item nav-link active">Order Status</a>
                    <a href="about.php" class="nav-item nav-link">About Us</a>
                    <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                </div>
                <a href="profCust.php" class="btn btn-primary py-2 px-4 mx-2">Profile</a>
            </div>
        </nav>
        <div class="container-xxl py-5 bg-dark hero-header mb-5">
            <div class="container text-center my-5 pt-5 pb-4">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Order Status & Details</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Order Status</li>
                    </ol>
                </nav>
            </div>
        </div>
        </div>
    
    <main>
        <section class="order-status-details">
            <div class="container">
                <div class="receipt-box wow fadeIn" data-wow-delay="0.1s">
                    <h2>Your Order Receipt</h2>
                    
                    <h4 style="color:#FEA116;">Order Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> <span class="text-primary">#<?php echo $order['order_id']; ?></span></p>
                            <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Payment Status:</strong> 
                                <span class="badge bg-<?php echo ($order['payment_status'] === 'Paid' ? 'success' : 'warning'); ?> p-2">
                                    <?php echo $order['payment_status']; ?>
                                </span>
                            </p>
                            <p><strong>Order Status:</strong> 
                                <span class="badge bg-info p-2">
                                    <?php echo $order['order_status']; ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <h4 style="color:#FEA116">Customer Details</h4>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_full_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                    <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id'] ?? 'N/A'); ?></p>

                    <h4 style="color:#FEA116;">Items Ordered</h4>
                    <ul class="list-group mb-4">
                        <?php while ($item = $items->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="my-0"><?php echo htmlspecialchars($item['food_name']); ?> x **<?php echo $item['quantity']; ?>**</h6>
                                    <small class="text-muted">Unit Price: RM <?php echo number_format($item['price_each'], 2); ?></small>
                                </div>
                                <span class="text-dark fw-bold">RM <?php echo number_format($item['price_each'] * $item['quantity'], 2); ?></span>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <h class="d-flex justify-content-between pt-3 border-top border-2 border-dark">
                        <span>**TOTAL AMOUNT PAID:**</span>
                        <strong class="text-primary">RM <?php echo number_format($order['total_amount'], 2); ?></strong>
                    </h3>

                    <hr class="my-4">
                    
                    <div class="no-print d-flex justify-content-between align-items-center">
                        <a href="menu.php" class="btn btn-secondary py-2 px-4">Back to Menu</a>
                        <?php echo $action_button; ?>
                    </div>
                    
                    <div class="no-print d-flex justify-content-end mt-3">
                         <a href="order_history.php" class="btn btn-link">View Order History List (Optional Page)</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Explore</h4>
                    <a class="btn btn-link" href="../index.php">Home</a>
                    <a class="btn btn-link" href="menu.php">Menu</a>
                    <a class="btn btn-link" href="about.php">About Us</a>
                    <a class="btn btn-link" href="contact.php">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+6011-62226128</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>toonpow43@gmail.com</p>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Opening Hours</h4>
                    <h5 class="text-light fw-normal">Monday - Saturday</h5>
                    <p>6.00pm - 11.00pm</p>
                    <h5 class="text-light fw-normal">Sunday</h5>
                    <p>5.00pm - 11.00pm</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Staff Portal</h4>
                    <a class="btn btn-link" href="../staff/staff_login.php">Staff Login</a>
                    <a class="btn btn-link" href="../admin/admin_login.php">Admin Login</a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Satay Kajang Uncle Ujang</a>, All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
</html>