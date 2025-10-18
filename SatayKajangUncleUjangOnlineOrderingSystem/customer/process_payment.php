<?php
session_start();
require_once '../connect.php';

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
$sql = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ? AND payment_status = 'Pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<script>alert('Invalid order!'); window.location.href='payment.php';</script>";
    exit;
}

// ToyyibPay API Credentials (ubah dengan yang sebenar)
$api_key = '8w3e2e9u-fo8d-s4j6-pni9-xbgt092ljifa';  // Dapatkan dari ToyyibPay dashboard
$category_code = '6yaipbmw';  // Kod kategori bill
$return_url = 'http://sataykajanguncleujang.com/customer/payment_callback.php';  // URL untuk redirect selepas payment
$callback_url = 'http://sataykajanguncleujang.com/customer/payment_callback.php';  // URL untuk callback

// Data untuk create bill
$bill_data = [
    'userSecretKey' => $api_key,
    'categoryCode' => $category_code,
    'billName' => 'Satay Kajang Order #' . $order_id,
    'billDescription' => 'Payment for Order #' . $order_id,
    'billPriceSetting' => 1,  // Fixed price
    'billPayorInfo' => 1,  // Collect payor info
    'billAmount' => $order['total_amount'] * 100,  // Amount in sen (ToyyibPay guna sen)
    'billReturnUrl' => $return_url,
    'billCallbackUrl' => $callback_url,
    'billExternalReferenceNo' => $order_id,  // Reference to order_id
    'billTo' => $_SESSION['customer_name'],
    'billEmail' => '',  // Tambah email jika ada dalam session
    'billPhone' => '',  // Tambah phone jika ada
];

// Send request to ToyyibPay API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://toyyibpay.com/api/createBill');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($bill_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result[0]['BillCode'])) {
    $bill_code = $result[0]['BillCode'];
    $payment_url = 'https://toyyibpay.com/' . $bill_code;

    // Simpan bill_code dalam database
    $update_sql = "UPDATE orders SET bill_code = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $bill_code, $order_id);
    $stmt->execute();
    $stmt->close();

    // Redirect ke ToyyibPay
    header("Location: $payment_url");
    exit;
} else {
    echo "<script>alert('Failed to create payment bill. Please try again.'); window.location.href='payment.php';</script>";
    exit;
}
?>