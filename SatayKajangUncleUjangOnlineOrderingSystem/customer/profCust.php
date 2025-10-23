<?php
// Include the database connection file.
// session_start() is expected to be in this file.
require_once '../connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Get customer info from session
$customer_id = $_SESSION['customer_id'];

// Define the variable for the header
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Prepare a SELECT statement to get the customer's current data
$stmt = $conn->prepare("SELECT name, email, phone_no, address, customer_image FROM customer WHERE customer_id = ?");
if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
}
$stmt->bind_param("i", $customer_id);


$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// Check if the form was submitted from a successful update
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message']);
    unset($_SESSION['error_message']);
}


if (isset($_FILES['customer_image']) && $_FILES['customer_image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['customer_image']['name'], PATHINFO_EXTENSION);
    $new_name = "cust_" . $customer_id . "." . $ext;
    $upload_dir = "../uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    $target = $upload_dir . $new_name;

    if (move_uploaded_file($_FILES['customer_image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("UPDATE customer SET customer_image = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $new_name, $customer_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Profile image updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to upload image.";
    }
} else {

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - About Us</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Satay Kajang Uncle Ujang, Malaysian satay, Kajang restaurant" name="keywords">
    <meta content="Learn about Satay Kajang Uncle Ujang, the best place for authentic Malaysian satay in Kajang." name="description">

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

.footer a:hover {
  color: #ffa500 !important; /* orange hover */
  transition: color 0.3s ease-in-out;
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
                    <h1 class="text-primary m-0">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo"><small>Satay Kajang Uncle Ujang</small></h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="../index.php" class="nav-item nav-link">Home</a>
                        <a href="menu.php" class="nav-item nav-link">Menu</a>
                        <a href="about.php" class="nav-item nav-link ">About Us</a>
                        <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                    </div>
                    <?php if ($is_loggedin): ?>
                        <a href="view_order_stat_cust.php" class="nav-item nav-link">Order Status</a>
                        <a href="profCust.php" class="btn btn-primary py-2 px-4 active">Profile</a>
                    <?php else: ?>
                        <a href="../register.php" class="btn btn-primary py-2 px-4 mx-2">Register</a>
                        <a href="../login.php" class="btn btn-primary py-2 px-4 mx-2">Login</a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Customer Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center text-uppercase">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">Customer Profile</h3>
                </div>
                <div class="card-body p-4">
                    <form action="upload_img_cust.php" method="POST" enctype="multipart/form-data" id="profile-form">
                        
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <!-- Profile Image -->
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <?php if (!empty($customer['customer_image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($customer['customer_image']); ?>" 
                                         alt="Profile Image" 
                                         class="rounded-circle border border-3 border-primary shadow-sm" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="../image/default-avatar.png" 
                                         alt="Default Avatar" 
                                         class="rounded-circle border border-3 border-secondary shadow-sm" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <div class="mb-4">
                            <label for="inputGroupFile01" class="form-label fw-bold">Change Profile Image</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="inputGroupFile01" name="customer_image" accept="image/*">
                                <button class="btn btn-primary" type="submit" name="upload_img">Upload</button>
                            </div>
                        </div>

                        <!-- Profile Info -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" name="name" 
                                   value="<?php echo htmlspecialchars($customer['name']); ?>" required disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($customer['email']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control" name="phone_no" 
                                   value="<?php echo htmlspecialchars($customer['phone_no'] ?? ''); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>" disabled>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-4">
                            <button type="button" id="edit-btn" class="btn btn-warning">Edit Profile</button>
                            <button type="submit" id="save-btn" name="update_profile" class="btn btn-success" style="display:none;">Save Changes</button>
                            <button type="button" id="cancel-btn" class="btn btn-secondary" style="display:none;">Cancel</button>
                            <a href="../change_pass.php" class="btn btn-info text-white">Change Password</a>
                            <a href="../logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




<script>
  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const inputs = document.querySelectorAll("#profile-form input, #profile-form textarea");

  // Simpan value asal
  let originalValues = {};
  inputs.forEach(input => {
    originalValues[input.name] = input.value;
  });

  // Klik Edit → enable semua field
  editBtn.addEventListener("click", () => {
    inputs.forEach(input => input.disabled = false);
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
  });

  // Klik Cancel → reset balik ke value asal
  cancelBtn.addEventListener("click", () => {
    inputs.forEach(input => {
      input.value = originalValues[input.name];
      input.disabled = true;
    });

    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";
  });
</script>

       
      </div>

<!-- Footer Start -->
<footer class="footer bg-dark text-light pt-5 pb-4">
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-3 col-md-6">
        <h5 class="text-uppercase fw-bold mb-4 text-warning">Explore</h5>
        <ul class="list-unstyled">
          <li><a href="index.php" class="text-light text-decoration-none d-block mb-2"><i class="fa fa-angle-right me-2"></i>Home</a></li>
          <li><a href="menu.php" class="text-light text-decoration-none d-block mb-2"><i class="fa fa-angle-right me-2"></i>Menu</a></li>
          <li><a href="about.php" class="text-light text-decoration-none d-block mb-2"><i class="fa fa-angle-right me-2"></i>About Us</a></li>
          <li><a href="contact.php" class="text-light text-decoration-none d-block"><i class="fa fa-angle-right me-2"></i>Contact Us</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5 class="text-uppercase fw-bold mb-4 text-warning">Contact</h5>
        <p><i class="fa fa-phone me-2"></i>+6011-62226128</p>
        <p><i class="fa fa-envelope me-2"></i>toonpow43@gmail.com</p>
        <p><i class="fa fa-map-marker-alt me-2"></i>1, Jalan Tps 1/6, Taman Pelangi, Semenyih</p>
        <div class="mt-3">
          <a href="#" class="text-light me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-light me-3"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5 class="text-uppercase fw-bold mb-4 text-warning">Opening Hours</h5>
        <p>Monday - Saturday<br>6.00pm - 11.00pm</p>
        <p>Sunday<br>5.00pm - 11.00pm</p>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5 class="text-uppercase fw-bold mb-4 text-warning">Staff Portal</h5>
        <ul class="list-unstyled">
          <li><a href="../staff/stafflogin.php" class="text-light text-decoration-none d-block mb-2"><i class="fa fa-angle-right me-2"></i>Staff Login</a></li>
          <li><a href="../admin/adminlogin.php" class="text-light text-decoration-none d-block"><i class="fa fa-angle-right me-2"></i>Admin Login</a></li>
        </ul>
      </div>
    </div>
    <hr class="mt-4 mb-3 border-light">
    <div class="text-center">
      <p class="mb-0 text-light">© Satay Kajang Uncle Ujang. All Rights Reserved.</p>
    </div>
  </div>
</footer>
<!-- Footer End -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../script/profCust.js"></script>

    <!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../lib/wow/wow.min.js"></script>
<script src="../lib/easing/easing.min.js"></script>
<script src="../lib/waypoints/waypoints.min.js"></script>
<script src="../lib/owlcarousel/owl.carousel.min.js"></script>
<script src="../lib/tempusdominus/js/moment.min.js"></script>
<script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="../js/main.js"></script>
<script src="../script/profCust.js"></script>

<!-- Spinner Hide on Load -->
<script>
    window.addEventListener('load', function() {
        const spinner = document.getElementById('spinner');
        if (spinner) spinner.classList.remove('show');
    });
</script>

</body>
</main>
</html>
