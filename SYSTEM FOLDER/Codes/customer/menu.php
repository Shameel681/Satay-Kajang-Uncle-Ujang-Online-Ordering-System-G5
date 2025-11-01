<?php
// Ensure session is started for access to $_SESSION
session_start();

// NOTE: Ensure connect.php handles database connection robustly
require_once '../connect.php';

// --- INITIALIZATION ---
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$is_loggedin = !empty($customer_id);
$customer_name = "Guest";
$menu_items = [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : []; // Initialize or retrieve cart

// Set minimum order quantity for Satay items
const SATAY_MIN_QTY = 5; 

// Check database connection once
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    // In a real scenario, you might redirect to an error page here
    // die("Connection failed: " . $conn->connect_error);
}

// Fetch Customer Name if logged in
if ($is_loggedin) {
    $stmt_customer = $conn->prepare("SELECT name FROM customer WHERE customer_id = ?");
    if ($stmt_customer === false) {
        // Log the error but don't stop the page
        error_log("Prepare failed (customer lookup): " . $conn->error);
    } else {
        $stmt_customer->bind_param("i", $customer_id);
        $stmt_customer->execute();
        $result_customer = $stmt_customer->get_result();
        $customer_data = $result_customer->fetch_assoc();
        $stmt_customer->close();

        if ($customer_data) {
            $customer_name = htmlspecialchars($customer_data['name']);
            $_SESSION['customer_name'] = $customer_name;
        }
    }
}

// --- AJAX HANDLER FOR CART OPERATIONS (ADD/UPDATE/REMOVE) ---
if (isset($_POST['action']) && isset($_POST['food_id'])) {
    // This is an AJAX request, so we should not output HTML content
    header('Content-Type: application/json');

    // *** PEMBETULAN KRITIKAL 1: food_id MESTI menjadi STRING, bukan integer ***
    // food_id dalam DB (menu.food_id) adalah VARCHAR (cth., F01, S02)
    $food_id = (string)$_POST['food_id'];
    $response = ['success' => false, 'message' => ''];

    // 1. Fetch item details (Name, Price, Category) for safety and cart display
    $stmt_item = $conn->prepare("SELECT food_name, price, category FROM menu WHERE food_id = ?");
    // Bind food_id sebagai string
    $stmt_item->bind_param("s", $food_id);
    $stmt_item->execute();
    $result_item = $stmt_item->get_result();
    $item_data = $result_item->fetch_assoc();
    $stmt_item->close();

    if (!$item_data) {
        $response['message'] = "Menu item not found.";
        echo json_encode($response);
        exit;
    }

    $item_data['price'] = (float)$item_data['price'];
    $is_satay = (strtolower($item_data['category']) == 'satay');
    
    // Process the action
    switch ($_POST['action']) {
        case 'add':
        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

            if ($quantity < 1) {
                $response['message'] = "Quantity must be at least 1.";
                break;
            }

            // Apply Satay minimum logic 
            if ($is_satay && $quantity < SATAY_MIN_QTY) {
                $response['message'] = "Satay items require a minimum order of " . SATAY_MIN_QTY . " skewers.";
                $response['code'] = 'MIN_QTY_FAIL'; 
                break;
            }

            // Item structure in cart: [food_id => ['name', 'price', 'qty', 'is_satay']]
            $cart[$food_id] = [
                'name' => $item_data['food_name'],
                'price' => $item_data['price'],
                'qty' => $quantity,
                'is_satay' => $is_satay
            ];
            $response['success'] = true;
            $response['message'] = "Cart updated successfully.";
            break;

        case 'remove':
            if (isset($cart[$food_id])) {
                unset($cart[$food_id]);
                $response['success'] = true;
                $response['message'] = "Item removed from cart.";
            } else {
                $response['message'] = "Item not in cart.";
            }
            break;
    }

    // Save the updated cart back to session
    $_SESSION['cart'] = $cart;
    
    // Recalculate cart total for the response
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }
    
    $response['cart_count'] = count($cart);
    $response['cart_total'] = number_format($total, 2, '.', '');
    $response['cart_items'] = $cart; // Send back cart data for client-side re-render

    echo json_encode($response);
    exit; // Stop further PHP execution for AJAX request
} 
// --- AJAX HANDLER FOR CLEAR CART (FIXED) ---
else if (isset($_POST['action']) && $_POST['action'] === 'clear_all') {
    // Fixed: Ensures a clean response and prevents the empty error
    header('Content-Type: application/json');
    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'message' => 'Cart cleared successfully.', 'cart_count' => 0, 'cart_total' => '0.00', 'cart_items' => []]);
    exit;
}
// --- AJAX HANDLER FOR CHECKOUT (FULLY FIXED) ---
else if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
    header('Content-Type: application/json');
    
    if (!$is_loggedin) {
        echo json_encode(['success' => false, 'message' => 'User must be logged in to checkout.', 'code' => 'NOT_LOGGED_IN']);
        exit;
    }
    
    $current_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    if (empty($current_cart)) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit;
    }
    
    // Start transaction to ensure order and details are saved atomically
    $conn->begin_transaction();
    $response = ['success' => false, 'message' => 'Order processing failed.'];

    try {
        $total_amount = 0;
        foreach ($current_cart as $item) {
            $total_amount += $item['price'] * $item['qty'];
        }
        
        // 1. Insert into the 'orders' table
        $order_date = date('Y-m-d H:i:s');
        $payment_status = 'Pending'; 
        $order_status = 'New Order'; 

        $stmt_order = $conn->prepare("INSERT INTO orders (customer_id, order_date, total_amount, payment_status, order_status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt_order === false) {
             throw new Exception("Prepare order failed: " . $conn->error);
        }
        // Binding parameters: i=integer, s=string, d=double/float, s=string, s=string
        $stmt_order->bind_param("isdss", $customer_id, $order_date, $total_amount, $payment_status, $order_status);
        
        if (!$stmt_order->execute()) {
             throw new Exception("Order insertion failed: " . $stmt_order->error);
        }
        
        $new_order_id = $conn->insert_id;
        $stmt_order->close();
        
        if (!$new_order_id) {
             throw new Exception("Failed to retrieve new order ID.");
        }

        // 2. Insert items into 'order_items' table (DB name)
        // Corrected table name to 'order_items' and column name to 'price_each'
        $stmt_detail = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price_each) VALUES (?, ?, ?, ?)");
        if ($stmt_detail === false) {
             throw new Exception("Prepare order detail failed: " . $conn->error);
        }
        
        foreach ($current_cart as $food_id => $item) {
            // *** PEMBETULAN KRITIKAL 2: food_id diuruskan sebagai STRING ***
            $food_id_string = (string)$food_id; // food_id is like 'F01' or 'S02'
            $quantity = $item['qty'];
            $price_each = $item['price']; // Harga unit
            
            // Binding parameters: i=order_id (int), s=food_id (string), i=quantity (int), s=price_each (DECIMAL/string)
            // Menggunakan "isis" untuk: int, string, int, string
            $stmt_detail->bind_param("isis", $new_order_id, $food_id_string, $quantity, $price_each);

            if (!$stmt_detail->execute()) {
                 throw new Exception("Order detail insertion failed for food ID $food_id: " . $stmt_detail->error);
            }
        }
        $stmt_detail->close();
        
        // 3. Commit transaction and clear session cart
        $conn->commit();
        unset($_SESSION['cart']); // Clear cart after successful order creation
        
        $response = [
            'success' => true,
            'message' => 'Order created successfully. Redirecting to payment.',
            'order_id' => $new_order_id,
            // Pass order_id to payment.php for specific fetching
            'redirect_url' => 'payment.php?order_id=' . $new_order_id 
        ];

    } catch (Exception $e) {
        $conn->rollback();
        // DEBUGGING OUTPUT
        error_log("Checkout Transaction Failed: " . $e->getMessage());
        
        $response['message'] = "Checkout failed: " . $e->getMessage(); 
    }

    echo json_encode($response);
    exit;
}

// --- NORMAL PAGE LOAD LOGIC (Menu Fetch) ---
if ($search !== '') {
    $sql = "SELECT food_id, food_name, price, image_path, description, category FROM menu 
            WHERE food_name LIKE ? OR category LIKE ?
            ORDER BY category, food_name";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Search prepare failed: " . $conn->error);
    } else {
        $like = "%$search%";
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();
    }
} else {
    $sql = "SELECT food_id, food_name, price, image_path, description, category FROM menu ORDER BY category, food_name";
    $result = $conn->query($sql);
}

// Process menu items
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price_float = (float)$row['price'];
        $row['price_formatted'] = number_format($price_float, 2, '.', '');
        // NOTE: As per your DB schema, 'Main Dish' and 'Side Dish' digunakan
        // Jika anda ingin menggunakan logik Satay, anda perlu memastikan kategori dalam DB adalah 'Satay' 
        // atau anda menukar logik di sini:
        // $row['is_satay'] = (strtolower($row['category']) == 'satay'); 
        
        // Menggunakan logik yang lebih umum berdasarkan nama makanan jika kategori DB tidak menggunakan 'Satay'
        $row['is_satay'] = (stripos($row['food_name'], 'Satay') !== false); 
        $menu_items[$row['category']][] = $row;
    }
    $result->free();
}

// Datalist Query
$datalist_options = [];
$sql_datalist = "SELECT DISTINCT food_name, category FROM menu ORDER BY food_name ASC";
$result_datalist = $conn->query($sql_datalist);
if ($result_datalist && $result_datalist->num_rows > 0) {
    while ($row = $result_datalist->fetch_assoc()) {
        $datalist_options[] = htmlspecialchars($row['food_name']);
        if (!in_array(htmlspecialchars($row['category']), $datalist_options)) {
            $datalist_options[] = htmlspecialchars($row['category']);
        }
    }
    $result_datalist->free();
    $datalist_options = array_unique($datalist_options); 
}

// Close connection before HTML output
if ($conn) {
    $conn->close();
}

// Calculate cart total for initial display
$cart_total = 0;
foreach ($cart as $item_id => $item) {
    $cart_total += $item['price'] * $item['qty'];
}
$cart_total_formatted = number_format($cart_total, 2, '.', '');
$cart_is_empty = empty($cart);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - Menu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../img/favicon.ico" rel="icon">

    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/menus.css">
    <link rel="stylesheet" href="../CSS/menus1.css">
</head>

<style>
    .navbar-brand img {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        object-fit: cover;
        margin-right: 10px;
        vertical-align: middle;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        text-align: center;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .product-modal .modal-header {
        background: linear-gradient(45deg, #e67e22, #d35400);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .success-modal .modal-header {
        background: linear-gradient(45deg, #28a745, #218838);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 10px;
    }
    .confirm-modal .modal-header {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 10px;
    }
    .modal-body {
        padding: 20px;
        font-size: 1.2rem;
        color: #333;
    }
    .modal-footer {
        padding: 10px;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .product-modal .modal-body img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    .quantity-control {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin: 15px 0;
    }
    .quantity-control button {
        background: #e67e22;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .quantity-control button:hover {
        background: #d35400;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(230, 126, 34, 0.3);
    }
    .quantity-control input {
        width: 60px;
        text-align: center;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1.1rem;
    }
    .btn-primary {
        background: linear-gradient(45deg, #e67e22, #d35400);
        border: none;
        padding: 10px 20px;
        color: white;
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(45deg, #d35400, #e67e22);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 126, 34, 0.4);
    }
    .btn-success {
        background: linear-gradient(45deg, #28a745, #218838);
        border: none;
        padding: 10px 20px;
        color: white;
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    .btn-success:hover {
        background: linear-gradient(45deg, #218838, #1e7e34);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }
    .btn-danger {
        background: linear-gradient(45deg, #dc3545, #c82333);
        border: none;
        padding: 10px 20px;
        color: white;
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    .btn-danger:hover {
        background: linear-gradient(45deg, #c82333, #bd2130);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
    }
    .cart-summary {
        background-color: #fff3e0; /* Light orange background for cart */
        border: 1px solid #ffcc80;
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .cart-items li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px dashed #ddd;
    }
    .cart-items li:last-child {
        border-bottom: none;
    }
    .cart-item-name {
        flex-grow: 1;
        text-align: left;
    }
    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .cart-item-price {
        min-width: 60px;
        text-align: right;
    }
    .cart-item-qty-input {
        width: 40px;
        text-align: center;
        padding: 2px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }
    .cart-item-btn {
        background: none;
        border: none;
        color: #d35400;
        cursor: pointer;
        font-size: 1rem;
        padding: 0 5px;
    }
    .cart-item-btn:hover {
        color: #e67e22;
    }

    /* New CSS for Slide-in Cart Sidebar */
    #cart-sidebar {
        position: fixed;
        top: 0;
        right: -400px;
        width: 400px;
        height: 100%;
        background-color: #fff;
        box-shadow: -2px 0 5px rgba(0,0,0,0.5);
        transition: right 0.3s ease-in-out;
        z-index: 1050;
        padding: 20px;
        overflow-y: auto;
    }
    #cart-sidebar.open {
        right: 0;
    }
    #cart-sidebar .close-sidebar {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
        cursor: pointer;
        color: #333;
    }
    #cart-sidebar .close-sidebar:hover {
        color: #d35400;
    }
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: none;
        z-index: 1040;
    }
    #overlay.active {
        display: block;
    }
    .cart-toggle {
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        background: linear-gradient(45deg, #e67e22, #d35400);
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .cart-toggle:hover {
        background: linear-gradient(45deg, #d35400, #e67e22);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 126, 34, 0.4);
    }
    .cart-toggle .badge {
        background-color: #fff;
        color: #d35400;
        margin-left: 8px;
        padding: 4px 8px;
        border-radius: 50%;
        font-size: 0.8rem;
    }
    .menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .search-form {
        flex-grow: 1;
        margin-right: 20px;
    }
    .search-form input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 25px;
    }
    .search-form button {
        padding: 8px 15px;
        background: linear-gradient(45deg, #e67e22, #d35400);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
    }
    .search-form button:hover {
        background: linear-gradient(45deg, #d35400, #e67e22);
    }
</style>

<body>
<div class="container-xxl bg-white p-0">
    <div class="container-xxl position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
            <a href="../index.php" class="navbar-brand p-0">
                <h1 class="text-primary m-0">
                    <img src="../image/LogoSataysebenarReal.png" alt="Logo" style="width:50px;height:50px;border-radius:50%;margin-right:10px;">
                    Satay Kajang Uncle Ujang
                </h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0 pe-4">
                    <a href="../index.php" class="nav-item nav-link">Home</a>
                    <a href="menu.php" class="nav-item nav-link active">Menu</a>
                    <a href="about.php" class="nav-item nav-link">About Us</a>
                    <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                    <?php if ($is_loggedin): ?>
                        <a href="view_order_stat_cust.php" class="nav-item nav-link">Order Status</a>
                    <?php endif?>
                </div>
                <?php if ($is_loggedin): ?>
                    <a href="profCust.php" class="btn btn-primary py-2 px-4 mx-2">Profile</a>
                <?php else: ?>
                    <a href="../register.php" class="btn btn-primary py-2 px-4 mx-2">Register</a>
                    <a href="../login.php" class="btn btn-primary py-2 px-4 mx-2">Login</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="container-xxl py-5 bg-dark hero-header mb-5">
            <div class="container text-center my-5 pt-5 pb-4">
                <h1 class="display-3 text-white mb-3 animated slideInDown">Food Menu</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">Menu</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <main>
        <section class="menu">
            <div class="container">
                <h2>Our Menu</h2>
                <div class="menu-header">
                    <form method="get" action="menu.php" class="search-form">
                        <input type="text" name="search" placeholder="Search for food or category..." list="foodSuggestions"
                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <datalist id="foodSuggestions">
                            <?php foreach ($datalist_options as $option): ?>
                                <option value="<?php echo $option; ?>">
                            <?php endforeach; ?>
                        </datalist>
                        <button type="submit"><i class="fa fa-search"></i> Search</button>
                    </form>
                    <a href="#" id="cart-toggle" class="cart-toggle"><i class="fa fa-shopping-cart"></i> Cart <span id="cart-badge" class="badge"><?php echo count($cart); ?></span></a>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?php if (!empty($menu_items)): ?>
                            <?php foreach ($menu_items as $category => $items): ?>
                                <div class="menu-section">
                                    <h3><?php echo htmlspecialchars($category); ?></h3>
                                    <ul class="menu-grid">
                                        <?php foreach ($items as $item): 
                                            $min_qty = $item['is_satay'] ? SATAY_MIN_QTY : 1;
                                        ?>
                                            <li class="menu-item"
                                                data-id="<?php echo htmlspecialchars($item['food_id']); ?>"
                                                data-name="<?php echo htmlspecialchars($item['food_name']); ?>"
                                                data-price="<?php echo htmlspecialchars($item['price']); ?>"
                                                data-image="../image/<?php echo htmlspecialchars($item['image_path']); ?>"
                                                data-description="<?php echo htmlspecialchars($item['description']); ?>"
                                                data-category="<?php echo htmlspecialchars($item['category']); ?>"
                                                data-is-satay="<?php echo $item['is_satay'] ? 'true' : 'false'; ?>"
                                                data-min-qty="<?php echo $min_qty; ?>">
                                                <img src="../image/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                                    alt="<?php echo htmlspecialchars($item['food_name']); ?>" 
                                                    class="menu-image">
                                                <div class="menu-details">
                                                    <h4>
                                                        <?php echo htmlspecialchars($item['food_name']); ?>
                                                        <span class="price">RM <?php echo $item['price_formatted']; ?></span>
                                                    </h4>
                                                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                                                    <button class="btn btn-primary add-to-cart">Add to Cart</button>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No menu items found<?php echo $search ? " for '".htmlspecialchars($search)."'" : ""; ?>. Please try a different search term or view the full menu.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Explore</h4>
                    <a class="btn btn-link" href="../index.php">Home</a>
                    <a class="btn btn-link" href="../customer/menu.php">Menu</a>
                    <a class="btn btn-link" href="../customer/about.php">About Us</a>
                    <a class="btn btn-link" href="../customer/contact.php">Contact Us</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+6011-62226128</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>toonpow43@gmail.com</p>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Opening Hours</h4>
                    <h5 class="text-light fw-normal">Monday - Saturday</h5>
                    <p>6.00pm - 11.00pm</p>
                    <h5 class="text-light fw-normal">Sunday</h5>
                    <p>5.00pm - 11.00pm</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Staff Portal</h4>
                    <a class="btn btn-link" href="../staff/staff_login.php">Staff Login</a>
                    <a class="btn btn-link" href="../admin/admin_login.php">Admin Login</a>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="#">Satay Kajang Uncle Ujang</a>, All Rights Reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Sidebar -->
    <div id="cart-sidebar">
        <span class="close-sidebar" id="close-sidebar">&times;</span>
        <div class="cart-summary">
            <h3><i class="fa-solid fa-shopping-cart me-2"></i> Your Cart</h3>
            <ul id="cart-items" class="list-unstyled cart-items">
            </ul>
            <div class="cart-total mt-3 pt-2 border-top">
                <strong>Total:</strong> <span id="total-price">RM <?php echo $cart_total_formatted; ?></span>
            </div>
            <div class="cart-actions d-flex flex-column mt-3">
                <button id="checkout-btn" class="btn btn-success mb-2" <?php echo $cart_is_empty ? 'disabled' : ''; ?>>Proceed to Checkout</button>
                <button id="clear-cart-btn" class="btn btn-danger" <?php echo $cart_is_empty ? 'disabled' : ''; ?>>Clear Cart</button>
            </div>
        </div>
    </div>

    <!-- Overlay for Sidebar -->
    <div id="overlay"></div>

    <div id="productModal" class="modal product-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa-solid fa-cart-plus me-2"></i> Add to Cart</h4>
                <span class="close" onclick="closeModal('productModal')">&times;</span>
            </div>
            <div class="modal-body">
                <img id="modal-image" src="" alt="">
                <h2 id="modal-title"></h2>
                <p id="modal-description"></p>
                <p id="modal-price"></p>
                <div class="quantity-control">
                    <button id="minus-btn" data-action="minus">−</button>
                    <input type="number" id="quantity-input" value="1" min="1">
                    <button id="plus-btn" data-action="plus">+</button>
                </div>
            </div>
            <div class="modal-footer">
                <button id="add-to-cart-btn" class="btn btn-primary" data-food-id="" data-is-satay="false" data-min-qty="1">Add to Cart</button>
            </div>
        </div>
    </div>

    <div id="custom-minimum-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header confirm-modal" style="background: #e67e22;">
                <h4><i class="fa-solid fa-triangle-exclamation me-2"></i> Quantity Warning</h4>
            </div>
            <div class="modal-body">
                <p>You need to order at least <span id="min-qty-text"><?php echo SATAY_MIN_QTY; ?></span> skewers for <span id="min-qty-food-name"></span>.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('custom-minimum-modal')" class="btn btn-primary">OK</button>
            </div>
        </div>
    </div>
    
    <div id="login-alert-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header confirm-modal" style="background: #dc3545;">
                <h4><i class="fa-solid fa-lock me-2"></i> Login Required</h4>
            </div>
            <div class="modal-body">
                <p><strong>Please Register or Sign in to place order</strong></p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.href='../login.php'" class="btn btn-primary">Go to Login</button>
                <button onclick="closeModal('login-alert-modal')" class="btn btn-success">Cancel</button>
            </div>
        </div>
    </div>

    <div id="checkout-success-modal" class="modal success-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa-solid fa-check-circle me-2"></i> Order Success!</h4>
            </div>
            <div class="modal-body">
                <p>Your order has been placed successfully!</p>
                <p>Order ID: <strong id="order-id"></strong></p>
                <p>Please proceed to payment.</p>
            </div>
            <div class="modal-footer">
                <button onclick="window.location.reload();" class="btn btn-success">Continue Shopping</button>
                <button id="make-payment-btn" class="btn btn-primary">Make Payment</button>
            </div>
        </div>
    </div>

    <div id="clear-cart-modal" class="modal confirm-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa-solid fa-trash me-2"></i> Confirm Clear Cart</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to clear your cart? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('clear-cart-modal')" class="btn btn-success">Cancel</button>
                <button id="clear-cart-confirm-btn" class="btn btn-danger">Yes, Clear</button>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- GLOBAL VARIABLES ---
        const isLoggedIn = <?php echo $is_loggedin ? 'true' : 'false'; ?>;
        let cart = <?php echo json_encode($cart); ?>; // Initial cart state from PHP session
        const SATAY_MIN_QTY = <?php echo SATAY_MIN_QTY; ?>;

        // --- HELPER FUNCTIONS ---
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // --- RENDER CART FUNCTION ---
        function renderCart(cartData) {
            const cartItemsList = document.getElementById('cart-items');
            const totalPriceSpan = document.getElementById('total-price');
            const checkoutBtn = document.getElementById('checkout-btn');
            const clearCartBtn = document.getElementById('clear-cart-btn');
            const cartBadge = document.getElementById('cart-badge');
            
            cartItemsList.innerHTML = '';
            let total = 0;
            const isEmpty = Object.keys(cartData).length === 0;

            if (isEmpty) {
                checkoutBtn.disabled = true;
                clearCartBtn.disabled = true;
            } else {
                checkoutBtn.disabled = false;
                clearCartBtn.disabled = false;
                
                for (const food_id in cartData) {
                    const item = cartData[food_id];
                    const itemTotal = item.price * item.qty;
                    total += itemTotal;
                    
                    // Determine minimum quantity for display
                    const minQty = item.is_satay ? SATAY_MIN_QTY : 1;

                    const listItem = document.createElement('li');
                    listItem.setAttribute('data-id', food_id);
                    listItem.innerHTML = `
                        <span class="cart-item-name">${item.name}</span>
                        <div class="cart-item-controls">
                            <button class="cart-item-btn update-qty-btn" data-id="${food_id}" data-action="minus" title="Kurang">-</button>
                            <input type="number" class="cart-item-qty-input" value="${item.qty}" min="${minQty}" readonly>
                            <button class="cart-item-btn update-qty-btn" data-id="${food_id}" data-action="plus" title="Tambah">+</button>
                            <span class="cart-item-price">RM ${itemTotal.toFixed(2)}</span>
                            <button class="cart-item-btn remove-item-btn" data-id="${food_id}" title="Buang"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    `;
                    cartItemsList.appendChild(listItem);
                }
            }
            
            totalPriceSpan.textContent = `RM ${total.toFixed(2)}`;
            cartBadge.textContent = Object.keys(cartData).length; // Update badge
        }

        // --- TOGGLE CART SIDEBAR ---
        function toggleCartSidebar(show = true) {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('overlay');
            if (show) {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            } else {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        }

        // --- AJAX FUNCTION TO UPDATE CART ---
        function updateCart(foodId, action, quantity = 1, isSatay = false) {
            let data = {
                action: action, 
                food_id: foodId
            };

            if (action === 'update' || action === 'plus' || action === 'minus') {
                // Logic to calculate the target quantity (newQty)
                let currentQty = cart[foodId] ? cart[foodId].qty : 0;
                let newQty = currentQty;
                const minQty = isSatay ? SATAY_MIN_QTY : 1;

                if (action === 'update') {
                    newQty = quantity;
                } else if (action === 'plus') {
                    newQty = currentQty + 1;
                } else if (action === 'minus') {
                    // Prevent quantity from falling below minimum allowed
                    newQty = Math.max(minQty, currentQty - 1); 
                }
                
                // If the new quantity is less than the min, show warning (client-side prevention)
                if (isSatay && newQty < minQty) {
                    if (currentQty === minQty && action === 'minus') {
                        // Already at minimum, stop the action.
                        return;
                    } 
                    if (currentQty === 0 || action === 'update') {
                        // User tried to add/update with insufficient quantity
                        document.getElementById('min-qty-food-name').textContent = cart[foodId] ? cart[foodId].name : document.querySelector(`.menu-item[data-id="${foodId}"]`).dataset.name;
                        document.getElementById('custom-minimum-modal').style.display = 'block';
                        return; 
                    }
                }
                
                data.action = 'update';
                data.quantity = newQty;
            } else if (action === 'clear_all') {
                // No foodId needed for clear_all, data is set above
                data = { action: 'clear_all' };
            }

            $.ajax({
                url: 'menu.php', // Current file
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        cart = response.cart_items; // Update global cart object
                        renderCart(cart); // Re-render the cart display
                        closeModal('productModal'); // Close modal on successful add
                        closeModal('clear-cart-modal'); // Close modal on successful clear
                    } else if (response.code === 'MIN_QTY_FAIL') {
                        // This case is primarily handled client-side but kept for server validation safety
                        document.getElementById('min-qty-food-name').textContent = cart[foodId] ? cart[foodId].name : document.querySelector(`.menu-item[data-id="${foodId}"]`).dataset.name;
                        document.getElementById('custom-minimum-modal').style.display = 'block';
                    } else {
                        // General error message
                        alert("Error: " + (response.message || "An unknown error occurred.")); 
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    alert("An error occurred while communicating with the server. Please check console.");
                }
            });
        }
        
        // --- EVENT LISTENERS ---
        $(document).ready(function() {
            // 1. Initial cart render
            renderCart(cart);

            // 2. Open Product Modal
            $('.add-to-cart').on('click', function() {
                const item = $(this).closest('.menu-item');
                const id = item.data('id');
                const name = item.data('name');
                const price = item.data('price');
                const image = item.data('image');
                const description = item.data('description');
                const isSatay = item.data('is-satay') === true || item.data('is-satay') === 'true'; 
                const minQty = parseInt(item.data('min-qty'));

                // Populate modal
                $('#modal-image').attr('src', image);
                $('#modal-title').text(name);
                $('#modal-description').text(description);
                $('#modal-price').text(`RM ${parseFloat(price).toFixed(2)} / stick`);
                
                // Set initial quantity: use cart quantity if exists, otherwise minQty or 1
                let initialQty = cart[id] ? cart[id].qty : minQty;

                $('#quantity-input').val(initialQty);
                $('#quantity-input').attr('min', minQty);
                
                // Update Add to Cart button data
                const addToCartBtn = $('#add-to-cart-btn');
                addToCartBtn.data('food-id', id);
                addToCartBtn.data('is-satay', isSatay);
                addToCartBtn.data('min-qty', minQty);
                
                $('#productModal').css('display', 'block');
            });
            
            // 3. Modal Quantity Control
            $('#minus-btn, #plus-btn').on('click', function() {
                const input = $('#quantity-input');
                let qty = parseInt(input.val());
                const minQty = parseInt(input.attr('min'));
                const action = $(this).data('action');
                
                if (action === 'plus') {
                    qty += 1;
                } else if (action === 'minus') {
                    qty = Math.max(minQty, qty - 1); // Ensure minimum quantity is respected
                }
                
                input.val(qty);
            });
            
            // 4. Final Add/Update from Modal
            $('#add-to-cart-btn').on('click', function() {
                const foodId = $(this).data('food-id');
                const isSatay = $(this).data('is-satay');
                const quantity = parseInt($('#quantity-input').val());
                const minQty = parseInt($(this).data('min-qty'));

                if (isSatay && quantity < minQty) {
                    document.getElementById('min-qty-food-name').textContent = $('#modal-title').text();
                    document.getElementById('custom-minimum-modal').style.display = 'block';
                    return;
                }
                
                updateCart(foodId, 'update', quantity, isSatay);
            });
            
            // 5. Update Cart Quantity from Cart Summary
            $(document).on('click', '.update-qty-btn', function() {
                const foodId = $(this).data('id');
                const action = $(this).data('action');
                const isSatay = cart[foodId].is_satay;
                
                // Pass current cart data for accurate calculation
                updateCart(foodId, action, cart[foodId].qty, isSatay); 
            });

            // 6. Remove Item from Cart Summary
            $(document).on('click', '.remove-item-btn', function() {
                const foodId = $(this).data('id');
                updateCart(foodId, 'remove');
            });

            // 7. Clear Cart Modal Trigger
            $('#clear-cart-btn').on('click', function() {
                $('#clear-cart-modal').css('display', 'block');
            });

            // 8. Clear Cart Confirmation
            $('#clear-cart-confirm-btn').on('click', function() {
                 updateCart(null, 'clear_all'); // Send a global clear action
            });

            // 9. Checkout
            $('#checkout-btn').on('click', function() {
                if (!isLoggedIn) {
                    $('#login-alert-modal').css('display', 'block');
                    return;
                }
                
                if (Object.keys(cart).length === 0) {
                    alert("Your cart is empty. Please add items before checking out.");
                    return;
                }
                
                // Send AJAX request to checkout
                $.ajax({
                    url: 'menu.php',
                    type: 'POST',
                    data: { action: 'checkout' },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#checkout-btn').prop('disabled', true).text('Processing...');
                    },
                    success: function(response) {
                        $('#checkout-btn').prop('disabled', false).text('Proceed to Checkout');
                        if (response.success) {
                            // Display success modal
                            $('#order-id').text(response.order_id);
                            $('#make-payment-btn').data('redirect-url', response.redirect_url); 
                            $('#checkout-success-modal').css('display', 'block');
                            
                            // Cart is cleared in PHP, so update client-side state
                            cart = {};
                            renderCart(cart);
                            toggleCartSidebar(false); // Close sidebar after checkout
                        } else if (response.code === 'NOT_LOGGED_IN') {
                            $('#login-alert-modal').css('display', 'block');
                        } else {
                            alert("Checkout failed: " + (response.message || "An unknown error occurred."));
                            if (response.debug_info) {
                                console.error("DEBUG INFO: ", response.debug_info);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#checkout-btn').prop('disabled', false).text('Proceed to Checkout');
                        console.error("Checkout AJAX Error:", status, error, xhr.responseText);
                        alert("An error occurred during checkout. Please try again.");
                    }
                });
            });

            // 10. Make Payment Button Redirect
            $('#make-payment-btn').on('click', function() {
                const redirectUrl = $(this).data('redirect-url');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    alert("Order ID not found. Please try checking out again.");
                }
            });

            // 11. Cart Sidebar Toggle
            $('#cart-toggle').on('click', function(e) {
                e.preventDefault();
                toggleCartSidebar(true);
            });

            $('#close-sidebar').on('click', function() {
                toggleCartSidebar(false);
            });

            $('#overlay').on('click', function() {
                toggleCartSidebar(false);
            });
        });
    </script>
</div>
</body>
</html>