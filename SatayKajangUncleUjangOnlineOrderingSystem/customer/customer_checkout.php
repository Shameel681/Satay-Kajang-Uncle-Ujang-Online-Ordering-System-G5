<?php
session_start();  // Tambah ini untuk akses $_SESSION
require_once '../connect.php';

header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to place an order.']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? 'Guest';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['cart']) || !is_array($data['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty or invalid.']);
    exit;
}

$total = 0;
foreach ($data['cart'] as $item) {
    $total += floatval($item['price']) * intval($item['quantity']);
}

try {
    $conn->begin_transaction();

    // Insert into orders table
    $sql_order = "INSERT INTO orders (customer_id, customer_name, order_date, total_amount, payment_status, order_status, receipt_sent)
                  VALUES (?, ?, NOW(), ?, 'Pending', 'Pending', 0)";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("isd", $customer_id, $customer_name, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert items into order_items table
    $sql_item = "INSERT INTO order_items (order_id, food_id, quantity, price_each)
                 VALUES (?, 
                    (SELECT food_id FROM menu WHERE food_name = ? LIMIT 1),
                    ?, ?)";
    $stmt_item = $conn->prepare($sql_item);

    foreach ($data['cart'] as $item) {
        $food_name = $item['name'];
        $quantity = intval($item['quantity']);
        $price_each = floatval($item['price']);
        $stmt_item->bind_param("isid", $order_id, $food_name, $quantity, $price_each);
        $stmt_item->execute();
    }

    $stmt_item->close();
    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>