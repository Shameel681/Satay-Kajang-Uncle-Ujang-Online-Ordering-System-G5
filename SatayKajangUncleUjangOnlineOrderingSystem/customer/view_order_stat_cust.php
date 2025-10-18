<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Ambil semua orders untuk customer
$sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Status</title>
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f7; font-family: Arial, sans-serif; }
        .order-box { background: #fff; max-width: 800px; margin: 50px auto; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="order-box">
        <h2>Your Order Status</h2>
        <?php if ($orders->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td>RM <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['payment_status']; ?></td>
                            <td><?php echo $order['order_status']; ?></td>
                            <td>
                                <?php if ($order['payment_status'] === 'Paid'): ?>
                                    <a href="view_receipt_cust.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-primary btn-sm">View Receipt</a>
                                <?php else: ?>
                                    <a href="payment.php" class="btn btn-warning btn-sm">Pay Now</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
        <a href="menu.php" class="btn btn-secondary">Back to Menu</a>
    </div>
</body>
</html>