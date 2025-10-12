<?php
// Ensure session is started for access to $_SESSION
session_start();

require_once '../connect.php'; 

// Check if the user is logged in
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$is_loggedin = !empty($customer_id);
$customer_name = "Guest";

if ($is_loggedin) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt_customer = $conn->prepare("SELECT name FROM customer WHERE customer_id = ?");
    if ($stmt_customer === false) {
        error_log("Prepare failed: " . $conn->error);
    } else {
        $stmt_customer->bind_param("i", $customer_id);
        $stmt_customer->execute();
        $result = $stmt_customer->get_result();
        $customer_data = $result->fetch_assoc();
        $stmt_customer->close();

        if ($customer_data) {
            $customer_name = htmlspecialchars($customer_data['name']);
            $_SESSION['customer_name'] = $customer_name;
        }
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$menu_items = [];

if ($search !== '') {
    $sql = "SELECT * FROM menu 
            WHERE food_name LIKE ? OR category LIKE ?
            ORDER BY category, food_name";
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
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
    $sql = "SELECT * FROM menu ORDER BY category, food_name";
    $result = $conn->query($sql);
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price_float = (float)$row['price'];
        $row['price_formatted'] = number_format($price_float, 2, '.', ''); 
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
    <link href="../img/favicon.ico" rel="icon">

    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="../CSS/bootstrap.min.css" rel="stylesheet">
    <link href="../CSS/styles.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/menus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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
        background-color: #fefefe;
        margin: 15% auto; 
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
        max-width: 500px;
        text-align: center;
        border-radius: 10px;
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

                <form method="get" action="menu.php" class="search-form">
                    <input type="text" name="search" placeholder="Search for food..." list="foodSuggestions"
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <datalist id="foodSuggestions">
                        <?php
                        $sql_datalist = "SELECT food_name FROM menu ORDER BY food_name ASC";
                        $result_datalist = $conn->query($sql_datalist);
                        if ($result_datalist && $result_datalist->num_rows > 0) {
                            while ($row = $result_datalist->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['food_name']) . "'>";
                            }
                        }
                        ?>
                    </datalist>
                    <button type="submit"><i class="fa fa-search"></i> Search</button>
                </form>

                <?php if (!empty($menu_items)): ?>
                    <?php foreach ($menu_items as $category => $items): ?>
                        <div class="menu-section">
                            <h2><?php echo htmlspecialchars($category); ?></h2>
                            <ul class="menu-grid">
                                <?php foreach ($items as $item): ?>
                                    <li class="menu-item"
                                        data-id="<?php echo htmlspecialchars($item['food_id']); ?>"
                                        data-name="<?php echo htmlspecialchars($item['food_name']); ?>"
                                        data-price="<?php echo htmlspecialchars($item['price_formatted']); ?>"
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
                                            <button class="btn btn-primary add-to-cart">Add to Cart</button>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No menu items found<?php echo $search ? " for '".htmlspecialchars($search)."'" : ""; ?>.</p>
                <?php endif; ?>

                <div id="cart-summary" class="cart-summary" style="display:none;">
                    <h3>Your Cart</h3>
                    <ul id="cart-items"></ul>
                    <div class="cart-total">
    <strong>Total:</strong> <span id="total-price">RM 0.00</span>
</div>
<div class="cart-actions d-flex justify-content-between mt-3">
    <button id="clear-cart-btn" class="btn btn-danger flex-fill me-2">Clear Cart</button>
    <button id="checkout-btn" class="btn btn-primary flex-fill ms-2">Checkout</button>
</div>


                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
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

    <!-- Existing Modals -->
    <div id="productModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <img id="modal-image" src="" alt="">
            <h2 id="modal-title"></h2>
            <p id="modal-description"></p>
            <p id="modal-price"></p>
            <div class="quantity-control">
                <button id="minus-btn">−</button>
                <input type="number" id="quantity-input" value="1" min="1">
                <button id="plus-btn">+</button>
            </div>
            <button id="add-to-cart-btn" class="btn">Add to Cart</button>
        </div>
    </div>

    <div id="custom-minimum-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <p>You need to order at least 5 skewers.</p>
            <button id="custom-modal-close-btn" class="btn">OK</button>
        </div>
    </div>

    <div id="login-alert-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <p><strong>Please Register or Sign in to place order</strong></p>
            <button id="login-alert-close-btn" class="btn">OK</button>
        </div>
    </div>

    <!-- ✅ New JS for popup + redirect -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const isLoggedIn = <?php echo $is_loggedin ? 'true' : 'false'; ?>;
        const loginAlertModal = document.getElementById('login-alert-modal');
        const loginAlertCloseBtn = document.getElementById('login-alert-close-btn');

        // Redirect to register on OK
        if (loginAlertCloseBtn) {
            loginAlertCloseBtn.addEventListener('click', function () {
                window.location.href = '../register.php';
            });
        }

        // Trigger modal for guests when Add to Cart is clicked
        document.querySelectorAll('#add-to-cart-btn, .add-to-cart, .addItemBtn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!isLoggedIn) {
                    e.preventDefault();
                    if (loginAlertModal) {
                        loginAlertModal.style.display = 'block';
                    }
                } else {
                    console.log("User is logged in - normal cart logic goes here.");
                }
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === loginAlertModal) {
                loginAlertModal.style.display = 'none';
            }
        };
    });
    </script>

<!-- ✅ JS: Login check + Cart system -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isLoggedIn = <?php echo $is_loggedin ? 'true' : 'false'; ?>;
    const loginAlertModal = document.getElementById('login-alert-modal');
    const loginAlertCloseBtn = document.getElementById('login-alert-close-btn');

    // ========== CART STORAGE ==========
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    function saveCart() {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    }

    function updateCartUI() {
        const cartItemsContainer = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');
        const cartSummary = document.getElementById('cart-summary');

        if (!cartItemsContainer || !totalPriceElement) return;

        cartItemsContainer.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            cartSummary.style.display = 'none';
            return;
        }

        cartSummary.style.display = 'block';
        cart.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} x${item.quantity} - RM ${item.price.toFixed(2)}`;
            cartItemsContainer.appendChild(li);
            total += item.price * item.quantity;
        });

        totalPriceElement.textContent = `RM ${total.toFixed(2)}`;
    }

    // ========== LOGIN POPUP ==========
    if (loginAlertCloseBtn) {
        loginAlertCloseBtn.addEventListener('click', function () {
            window.location.href = '../register.php';
        });
    }

    // ========== MODAL ELEMENTS ==========
    const productModal = document.getElementById('productModal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalDesc = document.getElementById('modal-description');
    const modalPrice = document.getElementById('modal-price');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const qtyInput = document.getElementById('quantity-input');
    const modalClose = document.querySelector('#productModal .close');

    let currentItem = null;

    // ========== ADD TO CART FLOW ==========
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            if (!isLoggedIn) {
                loginAlertModal.style.display = 'block';
                return;
            }

            // Show modal with item info
            const item = btn.closest('.menu-item');
            currentItem = {
                id: item.dataset.id,
                name: item.dataset.name,
                price: parseFloat(item.dataset.price),
                image: item.dataset.image,
                description: item.dataset.description
            };

            modalImage.src = currentItem.image;
            modalTitle.textContent = currentItem.name;
            modalDesc.textContent = currentItem.description;
            modalPrice.textContent = `RM ${currentItem.price.toFixed(2)}`;
            qtyInput.value = 1;

            productModal.style.display = 'block';
        });
    });

    // Quantity controls
    minusBtn.addEventListener('click', () => {
        let val = parseInt(qtyInput.value);
        if (val > 1) qtyInput.value = val - 1;
    });
    plusBtn.addEventListener('click', () => {
        let val = parseInt(qtyInput.value);
        qtyInput.value = val + 1;
    });

    // Confirm Add to Cart
    addToCartBtn.addEventListener('click', function () {
        const qty = parseInt(qtyInput.value);
        if (!currentItem) return;

        // Check if already in cart
        const existing = cart.find(i => i.id === currentItem.id);
        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push({ ...currentItem, quantity: qty });
        }

        saveCart();
        productModal.style.display = 'none';
    });

    // Close modal
    modalClose.addEventListener('click', () => {
        productModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === productModal) {
            productModal.style.display = 'none';
        }
        if (event.target === loginAlertModal) {
            loginAlertModal.style.display = 'none';
        }
    });

    // Load cart when page loads
    updateCartUI();

    // Checkout Button
    // Checkout Button
const checkoutBtn = document.getElementById('checkout-btn');
if (checkoutBtn) {
    checkoutBtn.addEventListener('click', async function () {
        if (!isLoggedIn) {
            loginAlertModal.style.display = 'block';
            return;
        }

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        try {
            const response = await fetch('customer_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart })
            });
            const data = await response.json();

            if (data.status === 'success') {
    alert('Order placed successfully!');
    cart = [];
    localStorage.removeItem('cart');
    updateCartUI(); // refresh UI instantly
    window.location.reload(); // optional: reload page for clean slate
} else {
    alert(data.message || 'Failed to save order.');
}

        } catch (err) {
            console.error(err);
            alert('Error communicating with the server.');
        }
    });
}

// ========== CLEAR CART BUTTON ==========
const clearCartBtn = document.getElementById('clear-cart-btn');
if (clearCartBtn) {
    clearCartBtn.addEventListener('click', function () {
        if (confirm("Are you sure you want to clear your cart?")) {
            cart = [];
            localStorage.removeItem('cart');
            updateCartUI();
        }
    });
}


});
</script>


</div>
</body>
</html>
