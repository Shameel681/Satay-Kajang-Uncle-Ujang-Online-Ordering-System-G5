<?php
// Include the database connection file which starts the session
require_once 'connect.php'; 

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$customer_name = $is_loggedin ? htmlspecialchars($_SESSION['name']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Menu</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="menu.php" class="active">Menu</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="profCust.php">Profile</a></li>
                    
                    <?php if ($is_loggedin): ?>
                    <li>
                        <a href="logout.php" class="btn">Logout</a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="register.php" class="btn">Register</a>
                    </li>
                    <li>
                        <a href="login.php" class="btn">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="menu">
            <div class="container">
                <h2>Our Menu</h2>
                <div class="menu-category">
                    <h3>Satay</h3>
                    <ul>
                        <li class="menu-item" data-name="Satay Ayam" data-price="1.00" data-image="image/satay ayam.png" data-description="Ayam diperap rempah rahsia, memanggang harum semerbak">
                            <img src="image/satay ayam.png" alt="Satay Ayam" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Ayam <span class="price">RM 1.00</span></h4>
                                <p>Ayam diperap rempah rahsia, memanggang harum semerbak</p>
                            </div>
                        </li>
                        <li class="menu-item" data-name="Satay Daging" data-price="1.20" data-image="image/satay daging.jpg" data-description="Daging dihiris halus, lembut dan penuh rasa">
                            <img src="image/satay daging.jpg" alt="Satay Daging" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Daging <span class="price">RM 1.20</span></h4>
                                <p>Daging dihiris halus, lembut dan penuh rasa</p>
                            </div>
                        </li>
                        <li class="menu-item" data-name="Satay Perut" data-price="1.20" data-image="image/satay perut.jpg" data-description="Perut direndam rempah, kenyal dan berperisa unik">
                            <img src="image/satay perut.jpg" alt="Satay Perut" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Perut <span class="price">RM 1.20</span></h4>
                                <p>Perut direndam rempah, kenyal dan berperisa unik</p>
                            </div>
                        </li>
                        <li class="menu-item" data-name="Satay Kambing" data-price="2.00" data-image="image/Satay kambing.jpg" data-description="Kambing dipanggang tepat, wangi dan tiada bau">
                            <img src="image/Satay kambing.jpg" alt="Satay Kambing" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Kambing <span class="price">RM 2.00</span></h4>
                                <p>Kambing dipanggang tepat, wangi dan tiada bau</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="menu-category">
                    <h3>Sides</h3>
                    <ul>
                        <li class="menu-item" data-name="Kuah Kacang" data-price="2.00" data-image="image/Kuah kacang.jpg" data-description="Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat">
                            <img src="image/Kuah kacang.jpg" alt="Kuah Kacang" class="menu-image">
                            <div class="menu-details">
                                <h4>Kuah Kacang <span class="price">RM 2.00</span></h4>
                                <p>Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat</p>
                            </div>
                        </li>
                        <li class="menu-item" data-name="Nasi Impit" data-price="1.50" data-image="image/Nasi Impit lagi.jpg" data-description="Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.">
                            <img src="image/Nasi Impit lagi.jpg" alt="Nasi Impit" class="menu-image">
                            <div class="menu-details">
                                <h4>Nasi Impit <span class="price">RM 1.50</span></h4>
                                <p>Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.</p>
                            </div>
                        </li>
                    </ul>
                </div>
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

        <div id="custom-minimum-modal" class="modal" style="display:none;">
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <h2>Minimum Order is 5 Skewers!</h2>
                <p>Please select at least 5 skewers to proceed.</p>
                <button id="custom-modal-close-btn" class="btn">OK</button>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© 2016 SATAY KAJANG UNCLE UJANG. All rights reserved.</p>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script src="script/menuscript.js"></script>
</body>
</html>