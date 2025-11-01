<?php
// Mula sesi dan sambungkan ke database
session_start();
require_once '../connect.php'; 

// Tukar URL asas ini kepada URL Ngrok anda. Pastikan ia TIDAK berakhir dengan "/"
$base_domain = 'https://sataykajanguncleujang.com'; 

// Penting untuk memastikan tiada output dihantar sebelum header
ob_start();

/**
 * Fungsi untuk menentukan status teks berdasarkan kod status ToyyibPay.
 * Standard ToyyibPay: 1=Paid, 2=Failed, 3=Pending/Varying Status (e.g., waiting for bank)
 * @param int $statusCode
 * @return array
 */
function getPaymentStatus($statusCode) {
    switch ($statusCode) {
        case 1:
            // 💰 PAID: Transaksi berjaya
            return ['payment' => 'Paid', 'order' => 'Processing']; 
        case 2:
            // ❌ FAILED: Transaksi bank gagal atau dibatalkan oleh pengguna
            return ['payment' => 'Failed', 'order' => 'Cancelled'];
        case 3:
            // 🕒 PENDING: Transaksi dalam proses / menunggu bank / expired.
            // Kita akan anggap gagal/dibatalkan untuk mengelakkan order diteruskan tanpa bayaran penuh.
            return ['payment' => 'Failed', 'order' => 'Cancelled']; 
        default:
            return ['payment' => 'Pending', 'order' => 'New'];
    }
}

// ====================================================================
// A. Handle CALLBACK dari ToyyibPay (POST) - MENGEMASKINI DATABASE (KRITIKAL)
// ====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tetapkan header untuk respons callback (ToyyibPay perlu respons 200 OK)
    header('Content-Type: text/plain');

    // Logging debug untuk menyemak apa yang ToyyibPay hantar (DITAMBAH: Log penuh untuk debug)
    $log_data = date('Y-m-d H:i:s') . " - Callback Received:\n" . print_r($_POST, true) . "\n---\n";
    file_put_contents('callback_log.txt', $log_data, FILE_APPEND);

    // Pastikan parameter utama dari ToyyibPay Callback wujud
    if (isset($_POST['billcode']) && isset($_POST['status_id']) && isset($_POST['order_id'])) {
        
        $bill_code = $_POST['billcode'];
        $statusId = intval($_POST['status_id']); 
        $transactionId = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
        $orderId = intval($_POST['order_id']); // Ini adalah billExternalReferenceNo yang kita hantar

        $statuses = getPaymentStatus($statusId);
        $payment_status = $statuses['payment'];
        $order_status = $statuses['order'];
        
        // LOGIK UPDATE KRITIKAL: Kemas kini status dan ID transaksi
        // Kita UPDATE hanya jika status_id BUKAN 3 (Pending)
        if ($statusId === 1 || $statusId === 2) {
            $sql = "UPDATE orders 
                    SET payment_status = ?, 
                        order_status = ?, 
                        transaction_id = ? 
                    WHERE order_id = ? AND bill_code = ? AND payment_status = 'Pending'";
            
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - SQL Prepare FAILED: " . $conn->error . "\n", FILE_APPEND);
                echo "ERROR";
                $conn->close();
                exit;
            }

            // Jenis parameter: s (payment_status), s (order_status), s (transaction_id), i (order_id), s (bill_code)
            $stmt->bind_param("sssis", $payment_status, $order_status, $transactionId, $orderId, $bill_code);
            
            if ($stmt->execute()) {
                 file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - DB Update SUCCESS for Order $orderId (Status: $payment_status)\n", FILE_APPEND);
            } else {
                 file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - DB Update FAILED for Order $orderId. Error: " . $conn->error . "\n", FILE_APPEND);
            }
            $stmt->close();
        } else {
             file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - Status ID 3 Received. No DB Update performed.\n", FILE_APPEND);
        }

        // ToyyibPay memerlukan respons 'OK' ringkas untuk setiap panggilan callback
        echo "OK"; 
        $conn->close();
        exit;
    }
    
    file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - ERROR: Missing Bill Code or Status ID in POST data.\n", FILE_APPEND);
    echo "ERROR: Missing Parameters";
    $conn->close();
    exit; 
}

// ====================================================================
// B. Handle RETURN dari ToyyibPay (GET) - REDIRECT USER
// ====================================================================

if (isset($_GET['billcode'])) {
    $bill_code = $_GET['billcode'];

    // Ambil order_id dan customer_id
    $sql = "SELECT order_id, customer_id FROM orders WHERE bill_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bill_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    $conn->close();
    ob_end_clean(); // Bersihkan output buffer sebelum redirect

    if ($order) {
        // Redirect ke halaman status order pelanggan
        $redirect_url = $base_domain . "/customer/view_order_stat_cust.php?order_id=" . $order['order_id'];
        
        header("Location: " . $redirect_url);
    } 
    
    else {
        // Bill code tidak ditemui
        $redirect_url = $base_domain . "/customer/menu.php";
        header("Location: " . $redirect_url);
    }
    exit;
}

// Jika tiada POST atau GET yang valid, redirect ke menu
$conn->close();
ob_end_clean(); 
$redirect_url = $base_domain . "/customer/menu.php";
header("Location: " . $redirect_url);
exit;
?>