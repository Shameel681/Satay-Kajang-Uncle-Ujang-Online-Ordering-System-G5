<?php
// Include the database connection file which starts the session
require_once '../connect.php'; 

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$customer_name = $is_loggedin ? htmlspecialchars($_SESSION['name']) : '';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$menu_items = [];

if ($search !== '') {
    $sql = "SELECT * FROM menu 
            WHERE food_name LIKE ? OR category LIKE ?
            ORDER BY category, food_name";
    $stmt = $conn->prepare($sql);
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM menu ORDER BY category, food_name";
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menu_items[$row['category']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Menu</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/menus.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="../index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="menu.php" class="active">Menu</a></li>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="contact.php">Contact us</a></li>
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php">Profile</a></li>
                    <?php else: ?>
                        <li><a href="../register.php" class="btn">Register</a></li>
                        <li><a href="../login.php" class="btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="menu">
            <div class="container">
                <h2>Our Menu</h2>

                <!-- 🔍 Search Bar -->
                <form method="get" action="menu.php" class="search-form">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search for food..." 
                        list="foodSuggestions" 
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                    >
                    <datalist id="foodSuggestions">
                        <?php
                        // Fetch all food names for suggestions
                        $sql = "SELECT food_name FROM menu ORDER BY food_name ASC";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['food_name']) . "'>";
                            }
                        }
                        ?>
                    </datalist>
                    <button type="submit"><i class="fa fa-search"></i> Search</button>
                </form>

                <?php if (!empty($menu_items)): ?>

                    <!-- ================= MAIN DISH SECTION ================= -->
                    <?php if (isset($menu_items['Main Dish'])): ?>
                        <div class="menu-section main-dish">
                            <h2>Main Dishes</h2>
                            <ul class="menu-grid">
                                <?php foreach ($menu_items['Main Dish'] as $item): ?>
                                    <li class="menu-item"
                                        data-name="<?php echo htmlspecialchars($item['food_name']); ?>"
                                        data-price="<?php echo htmlspecialchars(number_format($item['price'], 2)); ?>"
                                        data-image="../image/<?php echo htmlspecialchars($item['image_path']); ?>"
                                        data-description="<?php echo htmlspecialchars($item['description']); ?>">
                                        <img src="../image/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                            alt="<?php echo htmlspecialchars($item['food_name']); ?>" 
                                            class="menu-image">
                                        <div class="menu-details">
                                            <h4>
                                                <?php echo htmlspecialchars($item['food_name']); ?> 
                                                <span class="price">RM <?php echo number_format($item['price'], 2); ?></span>
                                            </h4>
                                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- ================= SIDE DISH SECTION ================= -->
                    <?php if (isset($menu_items['Side Dish'])): ?>
                        <div class="menu-section side-dish">
                            <h2>Side Dishes</h2>
                            <ul class="menu-center">
                                <?php foreach ($menu_items['Side Dish'] as $item): ?>
                                    <li class="menu-item"
                                        data-name="<?php echo htmlspecialchars($item['food_name']); ?>"
                                        data-price="<?php echo htmlspecialchars(number_format($item['price'], 2)); ?>"
                                        data-image="../image/<?php echo htmlspecialchars($item['image_path']); ?>"
                                        data-description="<?php echo htmlspecialchars($item['description']); ?>">
                                        <img src="../image/<?php echo htmlspecialchars($item['image_path']); ?>" 
                                            alt="<?php echo htmlspecialchars($item['food_name']); ?>" 
                                            class="menu-image">
                                        <div class="menu-details">
                                            <h4>
                                                <?php echo htmlspecialchars($item['food_name']); ?> 
                                                <span class="price">RM <?php echo number_format($item['price'], 2); ?></span>
                                            </h4>
                                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <p>No menu items found<?php echo $search ? " for '".htmlspecialchars($search)."'" : ""; ?>.</p>
                <?php endif; ?>

                <div id="cart-summary" class="cart-summary">
                    <h3>Your Cart</h3>
                    <ul id="cart-items"></ul>
                    <div class="cart-total">
                        <strong>Total:</strong> <span id="total-price">RM 0.00</span>
                    </div>
                    <button id="checkout-btn" class="btn">Checkout</button>
                </div>
            </div>
        </section>

        <!-- Product Modal -->
        <div id="productModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modal-title"></h2>
                <img id="modal-image" src="" alt="Product Image" class="modal-image">
                <p id="modal-description"></p>
                <p id="modal-price"></p>
                <div class="quantity-selector">
                    <button id="minus-btn">-</button>
                    <input type="number" id="quantity-input" value="1" min="1">
                    <button id="plus-btn">+</button>
                </div>
                <button id="add-to-cart-btn" class="btn">Add to Cart</button>
            </div>
        </div>

        <!-- Custom Minimum Modal -->
        <div id="custom-minimum-modal" class="modal" style="display:none;">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <h2>Minimum Order is 5 Skewers!</h2>
                <p>Please select at least 5 skewers to proceed.</p>
                <button id="custom-modal-close-btn" class="btn">OK</button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-row">
                <div class="footer-left">
                    <h3>Explore Our Page</h3>
                    <a href="../index.php">Home</a><br>
                    <a href="./menu.php">Menu</a><br>
                    <a href="./about.php">About Us</a><br>
                    <a href="./contact.php">Contact Us</a><br>
                </div>
                <div class="footer-right">
                    <h3>Staff & Admin</h3>
                    <a href="../staff/staff_login.php">Staff Login</a><br>
                    <a href="../admin/admin_login.php">Admin Login</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Satay Kajang Uncle Ujang. All rights reserved.</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../script/menuscript.js"></script>
</body>
</html>
