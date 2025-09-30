<?php
// Include the database connection file which starts the session
require_once '../connect.php'; 

// Check if the user is logged in
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$is_loggedin = !empty($customer_id);
$customer_name = "Guest";

// Only fetch customer name if a user is logged in
if ($is_loggedin) {
    $stmt_customer = $conn->prepare("SELECT name FROM customer WHERE customer_id = ?");
    $stmt_customer->bind_param("i", $customer_id);
    $stmt_customer->execute();
    $result = $stmt_customer->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt_customer->close();

    if ($customer_data) {
        $customer_name = htmlspecialchars($customer_data['name']);
    }
}

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
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - Menu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Satay Kajang Uncle Ujang, Malaysian satay, menu" name="keywords">
    <meta content="Explore the delicious menu at Satay Kajang Uncle Ujang, featuring authentic Malaysian satay and side dishes." name="description">

    <!-- Favicon -->
    <link href="../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../CSS/styles.css" rel="stylesheet">
     <link rel="stylesheet" href="../CSS/menus.css">

    <!-- Font Awesome for Search Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        .navbar-brand img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="../index.php" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><img src="../image/LogoSataysebenarReal.png" alt="Logo">Satay Kajang<br><small>Uncle Ujang</small></h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="../index.php" class="nav-item nav-link">Home</a>
                        <a href="menu.php" class="nav-item nav-link active">Menu</a>
                        <a href="about.php" class="nav-item nav-link">About Us</a>
                        <a href="contact.php" class="nav-item nav-link">Contact</a>
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
                            <li class="breadcrumb-item"><a href="#">Pages</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Menu</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Menu Content Start (Unchanged from Old menu.php) -->
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
        <!-- Menu Content End -->

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Explore</h4>
                        <a class="btn btn-link" href="../index.php">Home</a>
                        <a class="btn btn-link" href="menu.php">Menu</a>
                        <a class="btn btn-link" href="about.php">About Us</a>
                        <a class="btn btn-link" href="contact.php">Contact</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+6011-62226128</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i><a href="mailto:toonpow43@gmail.com">toonpow43@gmail.com</a></p>
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
                        <p>6:00 PM - 11:00 PM</p>
                        <h5 class="text-light fw-normal">Sunday</h5>
                        <p>5:00 PM - 11:00 PM</p>
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
        <!-- Footer End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
    <script src="../script/menuscript.js"></script>

    <!-- Fallback to hide spinner -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('spinner').classList.remove('show');
        });
    </script>
</body>
</html>