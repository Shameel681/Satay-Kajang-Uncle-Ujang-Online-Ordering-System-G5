<?php
session_start();
require_once '../connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Read cart data from POST request
$input = file_get_contents('php://input');
$cart = json_decode($input, true);

if (!$cart || empty($cart)) {
    echo json_encode(['success' => false, 'error' => 'Cart is empty']);
    exit;
}

// Calculate total amount
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert into orders table
    $order_sql = "INSERT INTO orders (customer_id, total_amount, payment_status, order_date) VALUES (?, ?, 'Pending', NOW())";
    $stmt = $conn->prepare($order_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("id", $customer_id, $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;
    $stmt->close();

    // Insert into order_details table
    $detail_sql = "INSERT INTO order_details (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($detail_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    foreach ($cart as $item) {
        $food_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $stmt->bind_param("iiid", $order_id, $food_id, $quantity, $price);
        $stmt->execute();
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'orderId' => $order_id]);
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>