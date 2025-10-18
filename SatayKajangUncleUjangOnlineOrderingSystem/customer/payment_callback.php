<?php
session_start();
require_once '../connect.php';

// Handle callback dari ToyyibPay (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['billcode']) && isset($data['status'])) {
        $bill_code = $data['billcode'];
        $status = $data['status'];  // 1 = Paid, 2 = Pending, 3 = Failed

        // Update payment status
        $payment_status = ($status == 1) ? 'Paid' : 'Pending';
        $sql = "UPDATE orders SET payment_status = ?, order_status = 'Processing' WHERE bill_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $payment_status, $bill_code);
        $stmt->execute();
        $stmt->close();

        // Jika paid, update receipt_sent atau hantar email (optional)
        if ($status == 1) {
            // Tambah logik untuk hantar receipt email jika perlu
        }

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Handle return dari ToyyibPay (GET, untuk redirect user)
if (isset($_GET['billcode'])) {
    $bill_code = $_GET['billcode'];

    // Ambil order berdasarkan bill_code
    $sql = "SELECT order_id FROM orders WHERE bill_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bill_code);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Redirect ke view receipt jika paid, atau view order status
        header("Location: view_receipt_cust.php?order_id=" . $order['order_id']);
    } else {
        header("Location: view_order_stat_cust.php");
    }
    exit;
}

// Jika tidak valid, redirect ke menu
header("Location: menu.php");
?>