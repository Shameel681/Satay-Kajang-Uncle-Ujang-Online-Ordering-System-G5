<?php
// Include the database connection file, which starts the session
require_once 'connect.php';
require 'vendor/autoload.php'; // autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables for messages
$message = '';
$message_type = '';

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($phone_no) || empty($password)) {
        $message = 'All fields are required.';
        $message_type = 'error';
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check_sql = "SELECT customer_id FROM customer WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = 'Registration failed. The email is already registered.';
            $message_type = 'error';
        } else {
            // generate verify token
            $verify_token = bin2hex(random_bytes(16));

            if (strlen($password) < 8) {
                $message = 'Password must be at least 8 characters long.';
                $message_type = 'error';
            } elseif (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
                $message = 'Phone number must be exactly 10 or 11 digits.';
                $message_type = 'error';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // insert new user with verify_token & is_verified=0
                $insert_sql = "INSERT INTO customer (name, email, phone_no, password, is_verified, verify_token) VALUES (?, ?, ?, ?, 0, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sssss", $name, $email, $phone_no, $password_hash, $verify_token);

                if ($insert_stmt->execute()) {
                    // Send verification email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'toonpow43@gmail.com';
                        $mail->Password   = 'mzyp uzsq aarf mmmq';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('youremail@gmail.com', 'Satay Kajang Uncle Ujang');
                        $mail->addAddress($email, $name);

                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Email Address';
                        $verify_link = "http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/verify.php?token=" . $verify_token;
                        $mail->Body    = "
                            <h3>Hi $name,</h3>
                            <p>Thank you for registering. Please click the link below to verify your email:</p>
                            <a href='$verify_link'>$verify_link</a>
                        ";

                        $mail->send();
                        $message = 'Registration successful! Please check your email to verify your account.';
                        $message_type = 'success';
                    } catch (Exception $e) {
                        $message = "Registration success but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Registration failed. Please try again.';
                    $message_type = 'error';
                }
                $insert_stmt->close();
            }
        }
        $check_stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register | Satay Kajang Uncle Ujang</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="CSS/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="CSS/styles.css" rel="stylesheet">
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
                <a href="" class="navbar-brand p-0">
                    
                    <h1 class="text-primary m-0"><img src="image/LogoSataysebenarReal.png" alt="Logo"><small>Satay Kajang Uncle Ujang</small></h1>
                    <!-- <img src="img/logo.png" alt="Logo"> -->
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
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Customer Registration</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center text-uppercase">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Register</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Registration Section -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="bg-light rounded p-5 shadow">
                <h2 class="text-center mb-4 text-primary">Customer Registration</h2>
                <p class="text-center text-muted mb-4">Join us and enjoy our authentic satay!</p>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Full Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-envelope"></i> Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-phone"></i> Phone Number</label>
                        <input type="text" class="form-control" name="phone" pattern="[0-9]{10,11}" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label"><i class="fa fa-lock"></i> Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" style="border-left: none;">
                                <i class="fa fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted">Password must be at least 8 characters long.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Registration Section End -->

<!-- Password Toggle Script -->
<script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    const toggleIcon = document.getElementById("toggleIcon");

    togglePassword.addEventListener("click", () => {
        const isHidden = password.type === "password";
        password.type = isHidden ? "text" : "password";
        toggleIcon.classList.toggle("fa-eye");
        toggleIcon.classList.toggle("fa-eye-slash");
    });
</script>


        <!-- Footer Start (same as index) -->
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h4 class="text-primary mb-4">Explore</h4>
                        <a class="btn btn-link" href="index.php">Home</a>
                        <a class="btn btn-link" href="customer/menu.php">Menu</a>
                        <a class="btn btn-link" href="customer/about.php">About Us</a>
                        <a class="btn btn-link" href="customer/contact.php">Contact Us</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="text-primary mb-4">Contact</h4>
                        <p><i class="fa fa-phone-alt me-3"></i>+6011-62226128</p>
                        <p><i class="fa fa-envelope me-3"></i>toonpow43@gmail.com</p>
                        <p><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih</p>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="text-primary mb-4">Opening Hours</h4>
                        <p>Mon-Sat: 6.00pm - 11.00pm<br>Sun: 5.00pm - 11.00pm</p>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="text-primary mb-4">Staff Portal</h4>
                        <a class="btn btn-link" href="staff/staff_login.php">Staff Login</a>
                        <a class="btn btn-link" href="admin/admin_login.php">Admin Login</a>
                    </div>
                </div>
            </div>
            <div class="text-center py-3 border-top border-light">
                &copy; 2025 Satay Kajang Uncle Ujang. All Rights Reserved.
            </div>
        </div>
        <!-- Footer End -->
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
