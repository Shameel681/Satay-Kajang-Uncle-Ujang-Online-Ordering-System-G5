<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Guest';

// Use order_id from URL if provided, else fetch latest pending order
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id > 0) {
    $sql = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ? AND payment_status = 'Pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $customer_id);
} else {
    $sql = "SELECT * FROM orders WHERE customer_id = ? AND payment_status = 'Pending' ORDER BY order_id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
}

$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<script>alert('No pending orders found!'); window.location.href='menu.php';</script>";
    exit;
}

$order_id = $order['order_id'];
$total_amount = number_format($order['total_amount'], 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Payment</title>
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f7f7;
            font-family: Arial, sans-serif;
        }
        .payment-box {
            background: #fff;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn-proceed {
            background: linear-gradient(45deg, #28a745, #218838);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            transition: 0.3s;
        }
        .btn-proceed:hover {
            background: linear-gradient(45deg, #218838, #1e7e34);
        }
    </style>
</head>
<body>
    <div class="payment-box text-center">
        <h2>Payment Confirmation</h2>
        <p>Hello, <strong><?php echo htmlspecialchars($customer_name); ?></strong></p>
        <p>Your Order ID: <strong>#<?php echo $order_id; ?></strong></p>
        <p>Total Amount: <strong>RM <?php echo $total_amount; ?></strong></p>

        <form action="process_payment.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <button type="submit" class="btn btn-proceed mt-3">Proceed to Payment</button>
        </form>

        <a href="menu.php" class="btn btn-secondary mt-3">Continue Shopping</a>
    </div>
</body>
</html>