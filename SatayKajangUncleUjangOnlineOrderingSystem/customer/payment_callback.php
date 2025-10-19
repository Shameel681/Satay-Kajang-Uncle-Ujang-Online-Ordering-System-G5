<?php
session_start();
require_once '../connect.php';

// 1. TUKAR NILAI INI dengan URL NGROK ANDA YANG SEDANG BERJALAN
// PASTIKAN URL INI SAMA TEPAT DENGAN YANG ANDA GUNA DALAM process_payment.php
$base_domain = 'https://unvacantly-hydroscopical-nieves.ngrok-free.dev/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem'; 

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
            return 'Pending';
    }
}

// ====================================================================
// A. Handle CALLBACK dari ToyyibPay (POST) - MENGEMASKINI DATABASE
// ====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['billcode']) && isset($_POST['status'])) {
        $bill_code = $_POST['billcode'];
        $statusCode = intval($_POST['status']);
        $transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;

        $payment_status = getPaymentStatus($statusCode);
        $order_status = ($payment_status === 'Paid') ? 'Processing' : 'New'; 

        // Sila pastikan anda telah menjalankan SQL ALTER TABLE!
        $sql = "UPDATE orders SET payment_status = ?, order_status = ?, transaction_id = ? WHERE bill_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $payment_status, $order_status, $transaction_id, $bill_code);
        $stmt->execute();
        $stmt->close();

        // ToyyibPay memerlukan respons 'success' yang ringkas
        echo "OK"; 
        exit;
    }
    exit; 
}

// ====================================================================
// B. Handle RETURN dari ToyyibPay (GET) - REDIRECT USER (DIPERBAIKI)
// ====================================================================

if (isset($_GET['billcode'])) {
    $bill_code = $_GET['billcode'];

    // Ambil order_id, payment_status, DAN customer_id (BARIS INI DIBETULKAN)
    $sql = "SELECT order_id, payment_status, customer_id FROM orders WHERE bill_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bill_code);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($order) {
        // 🚨 PENAMBAHAN KRITIKAL: Tetapkan semula sesi sebelum redirect 🚨
        // Ini memastikan pengguna tidak log out apabila diarahkan ke halaman seterusnya
        $_SESSION['customer_id'] = $order['customer_id'];
        
        if ($order['payment_status'] === 'Paid') {
            // Mesti tambah "/customer/"
            $redirect_url = $base_domain . "/customer/view_receipt_cust.php?order_id=" . $order['order_id'];
            header("Location: " . $redirect_url);
        } else {
            // Mesti tambah "/customer/"
            $redirect_url = $base_domain . "/customer/view_order_stat_cust.php";
            header("Location: " . $redirect_url);
        }
    } 
    
    else {
        // Jika bill_code tidak ditemui
        $redirect_url = $base_domain . "/customer/view_order_stat_cust.php";
        header("Location: " . $redirect_url);
    }
    exit;
}

// Jika tidak valid, redirect ke menu (menggunakan pautan mutlak)
$redirect_url = $base_domain . "/customer/menu.php";
header("Location: " . $redirect_url);
exit;