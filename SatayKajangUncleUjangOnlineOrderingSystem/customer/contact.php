<?php

require_once '../connect.php';

// Start a session if one is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$is_loggedin = !empty($customer_id);

// Initialize variables
$customer_name = "Guest";
$customer_email = null;

// Only fetch customer data if a user is logged in
if ($is_loggedin) {
    // Prepare and execute the query to get customer name and email
    $stmt_customer = $conn->prepare("SELECT name, email FROM customer WHERE customer_id = ?");
    $stmt_customer->bind_param("i", $customer_id);
    $stmt_customer->execute();
    $result = $stmt_customer->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt_customer->close();

    // If customer data is found, update the variables
    if ($customer_data) {
        $customer_name = htmlspecialchars($customer_data['name']);
        $customer_email = htmlspecialchars($customer_data['email']);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - Contact Us</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Satay Kajang Uncle Ujang, Malaysian satay, contact us" name="keywords">
    <meta content="Get in touch with Satay Kajang Uncle Ujang for inquiries, feedback, or bookings." name="description">

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

    <style>
        .navbar-brand img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            padding: 15px;
            border-radius: 5px;
            color: #fff;
            max-width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .toast.success {
            background-color: #28a745;
        }
        .toast.error {
            background-color: #dc3545;
        }
        .toast-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            float: right;
            margin-left: 10px;
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
                    <h1 class="text-primary m-0"><img src="../image/LogoSataysebenarReal.png" alt="Logo"><small>Satay Kajang Uncle Ujang</small></h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="../index.php" class="nav-item nav-link">Home</a>
                        <a href="menu.php" class="nav-item nav-link">Menu</a>
                        <a href="about.php" class="nav-item nav-link">About Us</a>
                        <a href="contact.php" class="nav-item nav-link active">Contact Us</a>
                    </div>
                    <?php if ($is_loggedin): ?>
                        <a href="view_order_stat_cust.php" class="nav-item nav-link">Order Status</a>
                        <a href="profCust.php" class="btn btn-primary py-2 px-4 mx-2">Profile</a>
                    <?php else: ?>
                        <a href="../register.php" class="btn btn-primary py-2 px-4 mx-2">Register</a>
                        <a href="../login.php" class="btn btn-primary py-2 px-4 mx-2">Login</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Contact Us</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center text-uppercase">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Contact</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Toast Notification -->
        <?php if (isset($_SESSION['feedback_success']) || isset($_SESSION['feedback_errors'])): ?>
            <div id="feedbackToast" class="toast <?php echo isset($_SESSION['feedback_success']) ? 'success' : 'error'; ?>">
                <span>
                    <?php
                    if (isset($_SESSION['feedback_success'])) {
                        echo htmlspecialchars($_SESSION['feedback_success']);
                        unset($_SESSION['feedback_success']);
                    } else {
                        foreach ($_SESSION['feedback_errors'] as $error) {
                            echo htmlspecialchars($error) . '<br>';
                        }
                        unset($_SESSION['feedback_errors']);
                    }
                    ?>
                </span>
                <button class="toast-close" aria-label="Close">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Contact Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h5 class="section-title ff-secondary text-center text-primary fw-normal">Contact Us</h5>
                    <h1 class="mb-5">Get in Touch with Us!</h1>
                </div>
                <div class="row g-4">
                    <div class="col-12">
                        <div class="row gy-4">
                            <div class="col-md-4">
                                <h5 class="section-title ff-secondary fw-normal text-start text-primary">Visit Us</h5>
                                <p><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="section-title ff-secondary fw-normal text-start text-primary">Contact</h5>
                                <p><i class="fa fa-phone-alt me-3"></i>011-62226128</p>
                                <p><i class="fa fa-envelope me-3"></i><a href="mailto:toonpow43@gmail.com">toonpow43@gmail.com</a></p>
                            </div>
                            <div class="col-md-4">
                                <h5 class="section-title ff-secondary fw-normal text-start text-primary">Opening Hours</h5>
                                <p>Monday - Saturday: 6:00 PM - 11:00 PM</p>
                                <p>Sunday: 5:00 PM - 11:00 PM</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 wow fadeIn" data-wow-delay="0.1s">
                        <iframe class="position-relative rounded w-100 h-100"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.534602808614!2d101.8576!3d2.9143!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdcac7a6c91c2b%3A0x5c89cbbdf8a6c5a1!2s1%2C%20Jalan%20Tps%201%2F6%2C%20Taman%20Pelangi%20Semenyih%2C%2043500%20Semenyih%2C%20Selangor!5e0!3m2!1sen!2smy!4v1735463899983!5m2!1sen!2smy"
                            frameborder="0" style="min-height: 350px; border:0;" allowfullscreen="" aria-hidden="false"
                            tabindex="0"></iframe>
                    </div>
                    <div class="col-md-6">
                        <div class="wow fadeInUp" data-wow-delay="0.2s">
                            <form id="contactForm" action="process_feedback.php" method="POST">
                                <h3 class="mb-4">Send Us Feedback</h3>
                                <div class="row g-3">
                                    <?php if ($is_loggedin): ?>
                                        <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($customer_name); ?>" readonly>
                                                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
                                                <label for="name">Your Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($customer_email); ?>" readonly>
                                                <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($customer_email); ?>">
                                                <label for="email">Your Email</label>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="guest_name" name="guest_name" placeholder="Your Name" required>
                                                <label for="guest_name">Your Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" id="guest_email" name="guest_email" placeholder="Your Email" required>
                                                <label for="guest_email">Your Email</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" placeholder="Leave your feedback here" id="feedback" name="feedback" style="height: 150px" required></textarea>
                                            <label for="feedback">Your Feedback</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary w-100 py-3" type="submit">Submit Feedback</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

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
    <script src="../script/feedback.js"></script>
    <!-- Fallback to hide spinner -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('spinner').classList.remove('show');
        });
    </script>
</body>
</html>