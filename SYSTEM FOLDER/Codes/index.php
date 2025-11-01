<?php
// PASTIKAN 'connect.php' MENGANDUNGI session_start();
// JIKA TIDAK, TAMBAH DI SINI: session_start();
require_once 'connect.php'; 

// BETULKAN: Menggunakan customer_id untuk cek login seperti dalam kod view_order_stat_cust.php
$is_loggedin = isset($_SESSION['customer_id']); 
// Anda boleh kekalkan semakan 'loggedin' jika itu adalah logik utama anda, 
// tetapi 'customer_id' lebih kuat. Saya kekalkan logik anda dengan 'loggedin'
// tetapi pastikan ia betul-betul dikemaskini semasa login.
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true; 

$customer_name = $is_loggedin ? htmlspecialchars($_SESSION['name']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - Home</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link href="img/favicon.ico" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <link href="CSS/bootstrap.min.css" rel="stylesheet">

    <link href="CSS/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    
                    <h1 class="text-primary m-0"><img src="image/LogoSataysebenarReal.png" alt="Logo"><small>Satay Kajang Uncle Ujang</small></h1>
                    <style>
                     .navbar-brand img {
                          border-radius: 50%;
                          width: 50px; /* Adjust size as needed */
                          height: 50px; /* Ensure equal width and height for a perfect circle */
                          object-fit: cover; /* Ensure the image fits within the circular frame */
                          margin-right: 10px; /* Space between logo and text */
                          vertical-align: middle; /* Align with text */
                      }
                   </style>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="index.php" class="nav-item nav-link active">Home</a>
                        <a href="customer/menu.php" class="nav-item nav-link">Menu</a>
                        <a href="customer/about.php" class="nav-item nav-link">About us</a>
                        <a href="customer/contact.php" class="nav-item nav-link">Contact Us</a>
                        
                        <?php if ($is_loggedin): ?>
                            <?php
                            // ⭐️ LOGIK BARU UNTUK PAUTAN STATUS ORDER DINAMIK ⭐️
                            $order_status_link = 'customer/view_order_stat_cust.php'; 

                            if (isset($_SESSION['last_viewed_order_id'])) {
                                $last_id = htmlspecialchars($_SESSION['last_viewed_order_id']);
                                // Arahkan ke Order ID terakhir yang dilihat
                                $order_status_link = 'customer/view_order_stat_cust.php?order_id=' . $last_id; 
                            }
                            ?>
                            <a href="<?php echo $order_status_link; ?>" class="nav-item nav-link">Order Status</a>
                        <?php endif?>
                    </div>
                    <?php if ($is_loggedin): ?>
                        <a href="customer/profCust.php" class="btn btn-primary py-2 px-4 mx-2">Profile</a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary py-2 px-4 mx-2">Register</a>
                        <a href="login.php" class="btn btn-primary py-2 px-4 mx-2">Login</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container my-5 py-5">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6 text-center text-lg-start">
                            <h1 class="display-3 text-white animated slideInLeft"><small>Belum Try Belum Tau,</small><br><small>Sudah Try Ingat Selalu</small></h1>
                            <p class="text-white animated slideInLeft mb-4 pb-2">Ramuan Rempah Ratus Turun Tangga</p>
                            <a href="customer/menu.php" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Menu</a>
                        </div>
                        <div class="col-lg-6 text-center text-lg-end overflow-hidden">
                            <img class="img-fluid" src="image/LogoBackHero.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-user-tie text-primary mb-4"></i>
                                <h5>Skilled Chefs</h5>
                                <p>Our chefs are experts in crafting authentic Satay Kajang, grilled to perfection over charcoal flames.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-utensils text-primary mb-4"></i>
                                <h5>Authentic Flavors</h5>
                                <p>Every bite is packed with traditional Malaysian spices and Uncle Ujang's secret marinade recipe.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-cart-plus text-primary mb-4"></i>
                                <h5>Easy Online Ordering</h5>
                                <p>Order your favorite satay and sides online for a quick and convenient dining experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="service-item rounded pt-3">
                            <div class="p-4">
                                <i class="fa fa-3x fa-headset text-primary mb-4"></i>
                                <h5>24/7 Support</h5>
                                <p>Our team is always ready to assist you with bookings, orders, or any inquiries.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-90 wow zoomIn" data-wow-delay="0.1s" src="image/sataybackabouut.jpg" alt="Satay grilling">
                            </div>
                            <div class="col-6 text-start">
                                <img class="img-fluid rounded w-95 wow zoomIn" data-wow-delay="0.3s" src="image/About back.jpg" style="margin-top: 79%;" alt="Satay preparation">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-95 wow zoomIn" data-wow-delay="0.5s" src="image/Kuah kacang.jpg" alt="Satay sauce">
                            </div>
                            <div class="col-6 text-end">
                                <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="image/jumbosatay.jpg" alt="Dining area">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                        <h1 class="mb-4">Welcome to <i class="fa fa-utensils text-primary me-2"></i>Satay Kajang Uncle Ujang</h1>
                        <p class="mb-4">At Satay Kajang Uncle Ujang, we bring you the authentic taste of Malaysian satay, crafted with recipes passed down through generations.</p>
                        <p class="mb-4">Our satay is marinated with a secret blend of spices and grilled over charcoal to deliver smoky, succulent flavors. Paired with our rich peanut sauce, every bite is a taste of tradition.</p>
                        <div class="row g-4 mb-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                    <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">10</h1>
                                    <div class="ps-4">
                                        <p class="mb-0">Years of</p>
                                        <h6 class="text-uppercase mb-0">Serving Satay</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                                    <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">5</h1>
                                    <div class="ps-4">
                                        <p class="mb-0">Skilled</p>
                                        <h6 class="text-uppercase mb-0">Satay Chefs</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="btn btn-primary py-3 px-5 mt-2" href="customer/about.php">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Our Menu</h5>
                    <h1 class="mb-5">Savor Our Signature Dishes</h1>
                </div>

                <?php
                // Query: get the latest 5 feedback entries
                $sql = "SELECT customer_name, customer_email, feedback, created_at FROM feedback_customer ORDER BY created_at DESC LIMIT 5";
                // NOTE: 'connect.php' dipanggil di awal, jadi tidak perlu panggil semula di sini.
                // Jika anda panggil 'connect.php' di dalam Testimonial, anda perlu pastikan $conn wujud di sini.
                
                // Fetch menu items grouped by category
                // Perlu pastikan $conn masih wujud dan terbuka untuk query ini.
                $sql_main = "SELECT * FROM menu WHERE category = 'Main Dish' LIMIT 4"; // Hadkan untuk 4 item sahaja di Home
                $sql_side = "SELECT * FROM menu WHERE category = 'Side Dish' LIMIT 4"; // Hadkan untuk 4 item sahaja di Home

                // Pastikan $conn wujud dari connect.php sebelum menjalankan query
                if (isset($conn)) {
                    $result_main = $conn->query($sql_main);
                    $result_side = $conn->query($sql_side);
                } else {
                    $result_main = false;
                    $result_side = false;
                    error_log("Database connection variable \$conn is not set in index.php");
                }
                ?>

                <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
                    <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 active" data-bs-toggle="pill" href="#tab-1">
                                <i class="fa fa-utensils fa-2x text-primary"></i>
                                <div class="ps-3">
                                    <small class="text-body">Popular</small>
                                    <h6 class="mt-n1 mb-0">Satay</h6>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="d-flex align-items-center text-start mx-3 pb-3" data-bs-toggle="pill" href="#tab-2">
                                <i class="fa fa-utensils fa-2x text-primary"></i>
                                <div class="ps-3">
                                    <small class="text-body">Special</small>
                                    <h6 class="mt-n1 mb-0">Sides</h6>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane fade show p-0 active">
                            <div class="row g-4">
                                <?php if ($result_main && $result_main->num_rows > 0): ?>
                                    <?php while ($row = $result_main->fetch_assoc()): ?>
                                        <div class="col-lg-6">
                                            <div class="d-flex align-items-center">
                                                <img class="flex-shrink-0 img-fluid rounded"
                                                src="<?php echo str_replace('../', '', htmlspecialchars($row['image_path'])); ?>"
                                                alt="<?php echo htmlspecialchars($row['food_name']); ?>"
                                                style="width: 80px;">
                                                <div class="w-100 d-flex flex-column text-start ps-4">
                                                    <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                        <span><?php echo htmlspecialchars($row['food_name']); ?></span>
                                                        <span class="text-primary">RM <?php echo number_format($row['price'], 2); ?></span>
                                                    </h5>
                                                    <small class="fst-italic"><?php echo htmlspecialchars($row['description']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="col-12 text-center">No main dishes found.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="tab-2" class="tab-pane fade show p-0">
                            <div class="row g-4">
                                <?php if ($result_side && $result_side->num_rows > 0): ?>
                                    <?php while ($row = $result_side->fetch_assoc()): ?>
                                        <div class="col-lg-6">
                                            <div class="d-flex align-items-center">
                                                <img class="flex-shrink-0 img-fluid rounded"
                                                src="<?php echo str_replace('../', '', htmlspecialchars($row['image_path'])); ?>"
                                                alt="<?php echo htmlspecialchars($row['food_name']); ?>"
                                                style="width: 80px;">

                                                <div class="w-100 d-flex flex-column text-start ps-4">
                                                    <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                        <span><?php echo htmlspecialchars($row['food_name']); ?></span>
                                                        <span class="text-primary">RM <?php echo number_format($row['price'], 2); ?></span>
                                                    </h5>
                                                    <small class="fst-italic"><?php echo htmlspecialchars($row['description']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="col-12 text-center">No side dishes found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container">
                <div class="text-center">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Testimonials</h5>
                    <h1 class="mb-5">What Our Customers Say!</h1>
                </div>
                <div class="owl-carousel testimonial-carousel">

                    <?php
                    // ⚠️ PEMBETULAN: Anda telah memanggil require_once 'connect.php'; di awal fail.
                    // Jika $conn telah ditutup di bahagian lain (walaupun tidak sepatutnya),
                    // ia mungkin menyebabkan ralat. Mari kita anggap $conn masih wujud dari awal.
                    
                    if (isset($conn)) {
                        // Query: get the latest 5 feedback entries
                        $sql = "SELECT customer_name, customer_email, feedback, created_at 
                                FROM feedback_customer 
                                ORDER BY created_at DESC 
                                LIMIT 5";

                        $result = $conn->query($sql);
                    } else {
                         // Fallback jika $conn tidak wujud
                        $result = false;
                    }

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $name = htmlspecialchars($row['customer_name']);
                            $email = htmlspecialchars($row['customer_email']);
                            $feedback = htmlspecialchars($row['feedback']);
                            $date = date("F j, Y", strtotime($row['created_at']));

                            echo '
                            <div class="testimonial-item bg-transparent border rounded p-4">
                                <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                                <p>' . $feedback . '</p>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 rounded-circle bg-light d-flex justify-content-center align-items-center" 
                                        style="width: 50px; height: 50px;">
                                        <i class="fa fa-user fa-lg text-primary"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h5 class="mb-1">' . $name . '</h5>
                                        <small>' . $email . '</small><br>
                                        <small class="text-muted">' . $date . '</small>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<p class="text-center text-muted">No feedback available yet.</p>';
                    }
                    // TIDAK PERLU menutup $conn di sini jika ia diperlukan di bahagian lain fail, 
                    // tetapi lebih baik jika ia ditutup di bahagian akhir fail jika tidak digunakan lagi.
                    if (isset($conn)) {
                        $conn->close();
                    }
                    ?>

                </div>
            </div>
        </div>
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Explore</h4>
                        <a class="btn btn-link" href="index.php">Home</a>
                        <a class="btn btn-link" href="customer/menu.php">Menu</a>
                        <a class="btn btn-link" href="customer/about.php">About Us</a>
                        <a class="btn btn-link" href="customer/contact.php">Contact Us</a>
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
                        <a class="btn btn-link" href="staff/staff_login.php">Staff Login</a>
                        <a class="btn btn-link" href="admin/admin_login.php">Admin Login</a>
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
        </div>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="js/main.js"></script>
</body>

</html>