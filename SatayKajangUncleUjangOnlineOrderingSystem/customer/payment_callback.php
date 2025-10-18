<?php
session_start();
require_once '../connect.php';

// Fungsi untuk menentukan status berdasarkan kod ToyyibPay
function getPaymentStatus($statusCode) {
    switch ($statusCode) {
        case 1:
            return 'Paid';
        case 2:
            return 'Pending';
        case 3:
            return 'Failed';
        default:
            return 'Pending'; // Default kepada Pending jika kod tidak diketahui
    }
}

// ====================================================================
// A. Handle CALLBACK dari ToyyibPay (POST) - MENGEMASKINI DATABASE
// ====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gunakan $_POST untuk menerima data callback dari ToyyibPay
    if (isset($_POST['billcode']) && isset($_POST['status'])) {
        $bill_code = $_POST['billcode'];
        $statusCode = intval($_POST['status']); // DITUKAR
        $transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;

        $payment_status = getPaymentStatus($statusCode); // DITUKAR

        // Hanya jika PAID, kita tetapkan order status kepada 'Processing'
        $order_status = ($payment_status === 'Paid') ? 'Processing' : 'New'; 

        // Update payment status dan simpan transaction_id jika ada
        $sql = "UPDATE orders SET payment_status = ?, order_status = ?, transaction_id = ? WHERE bill_code = ?";
        $stmt = $conn->prepare($sql);
        // Sila pastikan anda mempunyai lajur 'transaction_id' dalam jadual 'orders'
        $stmt->bind_param("ssss", $payment_status, $order_status, $transaction_id, $bill_code);
        $stmt->execute();
        $stmt->close();

        // ToyyibPay memerlukan respons 'success' yang ringkas
        echo "OK"; 
        exit;
    }
    
    // Jika data POST tidak lengkap, atau bukan dari ToyyibPay yang sah, kita diamkan sahaja
    exit; 
}

// ====================================================================
// B. Handle RETURN dari ToyyibPay (GET) - REDIRECT USER
// ====================================================================

if (isset($_GET['billcode'])) {
    $bill_code = $_GET['billcode'];

    // Ambil order_id
    $sql = "SELECT order_id, payment_status FROM orders WHERE bill_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bill_code);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($order) {
        if ($order['payment_status'] === 'Paid') {
            // Jika status PAID (telah dikemaskini oleh CALLBACK), terus ke resit
            header("Location: view_receipt_cust.php?order_id=" . $order['order_id']);
        } else {
            // Jika status lain (Pending/Failed), hantar ke halaman status umum untuk semak
            header("Location: view_order_stat_cust.php");
        }
    } else {
        // Jika bill_code tidak ditemui
        header("Location: view_order_stat_cust.php");
    }
    exit;
}

// Jika tidak valid, redirect ke menu
header("Location: menu.php");
exit;