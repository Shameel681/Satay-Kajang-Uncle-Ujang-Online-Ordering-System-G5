<?php
session_start();
require_once '../connect.php';

// --- CONFIGURATIONS TOYYIBPAY (SILA GANTI NILAI INI) ---
// ⚠️ PENTING: Gantikan 'SECRET_KEY_ANDA_YANG_SEBENAR' dengan Secret Key API dari dashboard ToyyibPay
$api_key = 'epepkahf-9ets-r608-u0sh-y1vmjvq89mtm';
// Category Code (Bill ID) yang diambil dari dashboard anda
$category_code = 'xcu9w5q4'; // Ini adalah Bill ID sebenar anda
// Gantikan dengan URL asas projek anda (tanpa /customer)
$base_domain = 'https://sataykajanguncleujang.com';
// --- AKHIR CONFIGURATIONS TOYYIBPAY ---

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['order_id'])) {
    header("Location: payment.php");
    exit;
}

$order_id = intval($_POST['order_id']);
$customer_id = $_SESSION['customer_id'];

// Ambil order details
$sql = "SELECT 
            o.*, 
            c.email AS customer_email, 
            c.phone_no AS customer_phone,
            c.name AS customer_full_name
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ? AND o.customer_id = ? AND o.payment_status = 'Pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<script>alert('Invalid order or order already paid!'); window.location.href='payment.php';</script>";
    exit;
}

// URL untuk redirect selepas payment
$return_url = $base_domain . '/customer/payment_callback.php';
// URL untuk callback (POST)
$callback_url = $base_domain . '/customer/payment_callback.php';

// Data untuk create bill
$bill_data = [
    'userSecretKey' => $api_key,
    'categoryCode' => $category_code,
    'billName' => 'Satay Kajang Order #' . $order_id,
    'billDescription' => 'Payment for Order #' . $order_id,
    'billPriceSetting' => 1,
    'billPayorInfo' => 1,
    'billAmount' => (int)($order['total_amount'] * 100), // Amount in sen
    'billReturnUrl' => $return_url,
    'billCallbackUrl' => $callback_url,
    'billExternalReferenceNo' => $order_id, 
    'billTo' => $order['customer_full_name'], // Guna nama penuh dari DB
    'billEmail' => $order['customer_email'], 
    'billPhone' => $order['customer_phone'], 
];

// Send request to ToyyibPay API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://dev.toyyibpay.com/api/createBill');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bill_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result[0]['BillCode'])) {
    $bill_code = $result[0]['BillCode'];
    $payment_url = 'https://dev.toyyibpay.com/' . $bill_code;

    // Simpan bill_code dalam database
    $update_sql = "UPDATE orders SET bill_code = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        die("SQL prepare failed (update): " . $conn->error);
    }
    $stmt->bind_param("si", $bill_code, $order_id);
    $stmt->execute();
    $stmt->close();

    // Redirect ke ToyyibPay
    header("Location: $payment_url");
    exit;
} else {
    // Log ralat untuk debugging
    error_log("ToyyibPay API Failed: " . $response);
    echo "<script>alert('Failed to create payment bill. Please try again.'); window.location.href='payment.php';</script>";
    exit;
}
?>