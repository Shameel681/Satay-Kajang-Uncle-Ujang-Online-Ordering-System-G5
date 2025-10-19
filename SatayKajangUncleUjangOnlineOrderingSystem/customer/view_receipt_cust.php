<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['customer_id']) || !isset($_GET['order_id'])) {
    header("Location: ../login.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$customer_id = $_SESSION['customer_id'];

// KOD TELAH DIBETULKAN: Mengambil bill_code dan transaction_id
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

// Semak status pembayaran HANYA di database (bukan di ToyyibPay)
if (!$order || $order['payment_status'] !== 'Paid') {
    echo "<script>alert('Receipt not available or order not paid.'); window.location.href='view_order_stat_cust.php';</script>";
    exit;
}

// Ambil order items
$sql_items = "SELECT oi.*, m.food_name FROM order_items oi JOIN menu m ON oi.food_id = m.food_id WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
$stmt_items->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Receipt #<?php echo $order['order_id']; ?></title>
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f7; font-family: Arial, sans-serif; }
        .receipt-box { 
            background: #fff; 
            max-width: 600px; 
            margin: 50px auto; 
            padding: 30px; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); 
        }
        /* Style untuk Print/Download */
        @media print {
            .no-print { display: none; }
            .receipt-box { box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <h2 class="mb-4">Official Order Receipt</h2>
        
        <p><strong>Order ID:</strong> #<?php echo $order['order_id']; ?></p>
        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($order['transaction_id'] ?? 'N/A'); ?></p>
        <hr>
        
        <h4>Customer Details:</h4>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_full_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
        <hr>
        
        <p><strong>Date:</strong> <?php echo date('d-M-Y H:i', strtotime($order['order_date'])); ?></p>
        <p><strong>Payment Status:</strong> <span class="badge bg-success"><?php echo $order['payment_status']; ?></span></p>
        <p><strong>Order Status:</strong> <span class="badge bg-info"><?php echo $order['order_status']; ?></span></p>

        <h4 class="mt-4">Items Ordered:</h4>
        <ul class="list-group mb-3">
            <?php while ($item = $items->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between lh-sm">
                    <div>
                        <h6 class="my-0"><?php echo htmlspecialchars($item['food_name']); ?> x<?php echo $item['quantity']; ?></h6>
                        <small class="text-muted">Unit Price: RM <?php echo number_format($item['price_each'], 2); ?></small>
                    </div>
                    <span class="text-muted">RM <?php echo number_format($item['price_each'] * $item['quantity'], 2); ?></span>
                </li>
            <?php endwhile; ?>
        </ul>

        <h4 class="d-flex justify-content-between">
            <span>Total Amount Paid:</span>
            <strong>RM <?php echo number_format($order['total_amount'], 2); ?></strong>
        </h4>

        <hr class="my-4">
        <div class="no-print">
            <a href="view_order_stat_cust.php" class="btn btn-secondary">Back to Orders</a>
            <button onclick="window.print()" class="btn btn-primary float-end">⬇️ Download/Print Receipt</button>
        </div>
    </div>
</body>
</html>