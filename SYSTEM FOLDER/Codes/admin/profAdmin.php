<?php
require_once '../connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin data
$stmt = $conn->prepare("SELECT admin_name, email, phone_no, address, profile_image, created_at FROM admin WHERE admin_id = ?");
if (!$stmt) {
    die("SQL Error: Please contact the administrator.");
}
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    die("Admin profile not found.");
}
$stmt->close();

// Success/Error message handling
$success_message = '';
$error_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = htmlspecialchars($_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = htmlspecialchars($_SESSION['error_message']);
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin Profile - Satay Kajang Uncle Ujang</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands"],
          urls: ["../assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    
    <style>
        .profile-avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #e67e22;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.3);
        }
        .profile-card {
            border-left: 5px solid #e67e22;
            box-shadow: 0 0 20px rgba(230, 126, 34, 0.1);
        }
        .form-label {
            font-weight: 600;
            color: #2c3e50;
        }
        .readonly-field {
            background-color: #f8f9fa;
            opacity: 0.7;
            cursor: not-allowed;
        }
        .readonly-field:focus {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            box-shadow: none;
        }
        .profile-actions .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .image-upload-btn {
            background: linear-gradient(45deg, #e67e22, #d35400);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            transition: all 0.3s ease;
        }
        .image-upload-btn:hover {
            background: linear-gradient(45deg, #d35400, #e67e22);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.3);
        }
        .edit-mode .readonly-field {
            background-color: white;
            opacity: 1;
            cursor: text;
        }
        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e67e22, #d35400);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            border: 4px solid #e67e22;
            transition: all 0.3s ease;
        }
        .avatar-placeholder:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="admin_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Uncle Ujang" class="navbar-brand" height="30" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item">
                            <a href="admin_dashboard.php">
                                <i class="fa-solid fa-gauge"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admincustomer.php">
                                <i class="fa-solid fa-users"></i>
                                <p>Manage Customer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="adminstaff.php">
                                <i class="fa-solid fa-user-tie"></i>
                                <p>Manage Staff</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_menu.php">
                                <i class="fa-solid fa-utensils"></i>
                                <p>View Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_manageorder.php">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <p>Manage Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_viewsale.php">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <p>View Sales</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="profAdmin.php">
                                <i class="fa-solid fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_logout.php">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="admin_dashboard.php" class="logo">
                            <img src="../image/LogoSataysebenarReal.png" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                </div>
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Search profile..." class="form-control" disabled />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?= htmlspecialchars($admin['admin_name']) ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profAdmin.php">
                                            <i class="fa-solid fa-user me-2"></i> My Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="../admin/change_admin.php">
                                            <i class="fa-solid fa-lock me-2"></i> Change Password
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="admin_logout.php">
                                            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="container">
                <div class="page-inner">
                    <!-- Header -->
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">
                                <i class="fa-solid fa-user text-primary me-2"></i>
                                My Profile
                            </h1>
                            <p class="op-7 mb-2">Manage your admin account information and preferences</p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-check-circle me-2"></i>
                            <?= $success_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Card -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card profile-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="fa-solid fa-id-card me-2"></i>
                                        Profile Information
                                    </h4>
                                    <div>
                                        <span class="badge badge-success">
                                            <i class="fa-solid fa-check me-1"></i>Verified
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Profile Header with Image -->
                                    <div class="row mb-4">
                                        <div class="col-md-2 text-center mb-3">
                                            <div class="position-relative">
                                                <?php if (!empty($admin['profile_image'])): ?>
                                                    <img src="../uploads/admin/<?= htmlspecialchars($admin['profile_image']) ?>" 
                                                         alt="Profile" class="profile-avatar mx-auto d-block" />
                                                <?php else: ?>
                                                    <div class="avatar-placeholder mx-auto d-block">
                                                        <?= strtoupper(substr($admin['admin_name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <form action="upload_img_admin.php" method="POST" enctype="multipart/form-data" id="imageForm" style="display: inline;">
                                                    <input type="file" name="profile_image" id="profileImage" accept="image/*" class="d-none" style="display: none;">
                                                    <label for="profileImage" class="image-upload-btn mt-2 d-block mx-auto">
                                                        <i class="fa-solid fa-camera me-1"></i>Change Photo
                                                    </label>
                                                    <button type="submit" class="btn btn-sm btn-primary d-none" id="uploadBtn">
                                                        <i class="fa-solid fa-upload me-1"></i>Upload
                                                    </button>
                                                </form>
                                            </div>
                                            <small class="text-muted d-block mt-2">Click camera icon to change</small>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h3 class="fw-bold mb-1"><?= htmlspecialchars($admin['admin_name']) ?></h3>
                                                    <p class="text-muted mb-0">Administrator</p>
                                                    <p class="text-primary fw-semibold">ADMIN<?= sprintf('%04d', $admin_id) ?></p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <span class="badge badge-success me-2">
                                                        <i class="fa-solid fa-circle-check me-1"></i>Active
                                                    </span>
                                                    <span class="badge badge-info">
                                                        <i class="fa-solid fa-calendar me-1"></i>
                                                        <?= date('M Y', strtotime($admin['created_at'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Profile Info Form -->
                                    <form action="profAdminUpdate.php" method="POST" id="profileForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email Address</label>
                                                    <input type="email" 
                                                           class="form-control readonly-field" 
                                                           value="<?= htmlspecialchars($admin['email']) ?>" 
                                                           readonly>
                                                    <div class="form-text text-muted">Email cannot be changed</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Admin ID</label>
                                                    <input type="text" 
                                                           class="form-control bg-light" 
                                                           value="ADMIN<?= sprintf('%04d', $admin_id) ?>" 
                                                           readonly>
                                                    <div class="form-text text-muted">Your unique admin identifier</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           name="phone_no" 
                                                           class="form-control readonly-field" 
                                                           value="<?= htmlspecialchars($admin['phone_no'] ?? '') ?>" 
                                                           pattern="[0-9]{10,11}" 
                                                           title="Phone number must be 10-11 digits"
                                                           disabled>
                                                    <div class="form-text">10-11 digits only (e.g., 0123456789)</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" 
                                                           name="admin_name" 
                                                           class="form-control readonly-field" 
                                                           value="<?= htmlspecialchars($admin['admin_name']) ?>" 
                                                           required 
                                                           disabled>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Address</label>
                                                    <textarea name="address" 
                                                              class="form-control readonly-field" 
                                                              rows="3" 
                                                              placeholder="Enter your full address" 
                                                              disabled><?= htmlspecialchars($admin['address'] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="update_profile" value="1">
                                        <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
                                    </form>
                                </div>
                                <div class="card-footer profile-actions bg-light p-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" id="editBtn" class="btn btn-primary">
                                            <i class="fa-solid fa-edit me-2"></i>Edit Profile
                                        </button>
                                        <button type="button" id="saveBtn" class="btn btn-success" style="display: none;">
                                            <i class="fa-solid fa-save me-2"></i>Save Changes
                                        </button>
                                        <button type="button" id="cancelBtn" class="btn btn-secondary" style="display: none;">
                                            <i class="fa-solid fa-times me-2"></i>Cancel
                                        </button>
                                        <a href="../admin/change_admin.php" class="btn btn-warning">
                                            <i class="fa-solid fa-lock me-2"></i>Change Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Stats -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fa-solid fa-shield-alt"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Account Status</p>
                                                <h4 class="card-title">
                                                    <span class="badge badge-success">Active</span>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fa-solid fa-user-crown"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Role</p>
                                                <h4 class="card-title text-warning">Administrator</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fa-solid fa-calendar"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Member Since</p>
                                                <h4 class="card-title"><?= date('M Y', strtotime($admin['created_at'])) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="fa-solid fa-cog"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Last Updated</p>
                                                <h4 class="card-title"><?= date('M d, Y') ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright">
                        © 2025 Satay Kajang Uncle Ujang. All rights reserved.
                    </div>
                </footer>
            </div>
        </div>

        <!-- Core JS Files -->
        <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>
        <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
        <script src="../assets/js/kaiadmin.min.js"></script>

        <script>
            // Elements
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const profileForm = document.getElementById('profileForm');
            const imageForm = document.getElementById('imageForm');
            const profileImageInput = document.getElementById('profileImage');
            const uploadBtn = document.getElementById('uploadBtn');

            let originalValues = {};
            const inputs = profileForm.querySelectorAll('input:not([readonly]), textarea');

            // Store original values
            inputs.forEach(input => {
                originalValues[input.name] = input.value;
            });

            // Edit mode
            editBtn.addEventListener('click', () => {
                document.body.classList.add('edit-mode');
                inputs.forEach(input => {
                    if (!input.readOnly) {
                        input.disabled = false;
                        input.classList.remove('readonly-field');
                    }
                });
                editBtn.style.display = 'none';
                saveBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';
                inputs[0].focus();
            });

            // Cancel edit
            cancelBtn.addEventListener('click', () => {
                document.body.classList.remove('edit-mode');
                inputs.forEach(input => {
                    if (originalValues[input.name] !== undefined) {
                        input.value = originalValues[input.name];
                        input.disabled = true;
                        input.classList.add('readonly-field');
                    }
                });
                editBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            });

            // Save profile changes
            saveBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to save these changes?')) {
                    // Validate phone number
                    const phoneInput = profileForm.querySelector('input[name="phone_no"]');
                    const phoneRegex = /^[0-9]{10,11}$/;
                    if (!phoneRegex.test(phoneInput.value)) {
                        alert('Phone number must be 10 or 11 digits only.');
                        phoneInput.focus();
                        return;
                    }
                    profileForm.submit();
                }
            });

            // Image upload
            profileImageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/') && file.size <= 2 * 1024 * 1024) {
                    uploadBtn.style.display = 'inline-block';
                    uploadBtn.innerHTML = `<i class="fa-solid fa-upload me-1"></i>Upload ${file.name}`;
                    
                    if (confirm('Upload this image now?')) {
                        imageForm.submit();
                    }
                } else {
                    alert('Please select a valid image file (JPG, PNG) under 2MB.');
                    profileImageInput.value = '';
                }
            });

            // Auto-dismiss alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        </script>
    </body>
</html>