<?php
// admin/admin_menu.php
require_once '../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: profAdmin.php");
    exit;
}

// Sample menu data - dalam real implementation, fetch dari database
$menu_items = [
    [
        'id' => 1,
        'name' => 'Satay Ayam',
        'category' => 'Satay',
        'description' => 'Ayam diperap rempah rahsia, memanggang harum semerbak',
        'price' => 1.00,
        'image' => '../image/satay ayam.png',
        'status' => 'available'
    ],
    [
        'id' => 2,
        'name' => 'Satay Daging',
        'category' => 'Satay',
        'description' => 'Daging dihiris halus, lembut dan penuh rasa',
        'price' => 1.20,
        'image' => '../image/satay daging.jpg',
        'status' => 'available'
    ],
    [
        'id' => 3,
        'name' => 'Satay Perut',
        'category' => 'Satay',
        'description' => 'Perut direndam rempah, kenyal dan berperisa unik',
        'price' => 1.20,
        'image' => '../image/satay perut.jpg',
        'status' => 'available'
    ],
    [
        'id' => 4,
        'name' => 'Satay Kambing',
        'category' => 'Satay',
        'description' => 'Kambing dipanggang tepat, wangi dan tiada bau',
        'price' => 2.00,
        'image' => '../image/Satay kambing.jpg',
        'status' => 'available'
    ],
    [
        'id' => 5,
        'name' => 'Kuah Kacang',
        'category' => 'Sides',
        'description' => 'Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat',
        'price' => 2.00,
        'image' => '../image/Kuah kacang.jpg',
        'status' => 'available'
    ],
    [
        'id' => 6,
        'name' => 'Nasi Impit',
        'category' => 'Sides',
        'description' => 'Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.',
        'price' => 1.50,
        'image' => '../image/Nasi Impit lagi.jpg',
        'status' => 'available'
    ]
];

// Group by category
$categories = [];
foreach ($menu_items as $item) {
    $categories[$item['category']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>View Menu - Satay Kajang Uncle Ujang</title>
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
        .menu-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .menu-image {
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }
        .category-header {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
            margin-bottom: 1rem;
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
                                <i class="fa-solid fa-utensils"></i>
                                <p>Manage Staff</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="admin_menu.php">
                                <i class="fa-solid fa-utensils"></i>
                                <p>View Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
                            </a>
                        </li>
                        <li class="nav-item">
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
                                <input type="text" placeholder="Search menu items..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="../assets/img/profile.jpg" alt="Admin" class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profAdmin.php">
                                            <i class="fa-solid fa-user me-2"></i> Profile
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
                                <i class="fa-solid fa-utensils text-warning me-2"></i>
                                Restaurant Menu
                            </h1>
                            <p class="op-7 mb-2">View and manage Satay Kajang Uncle Ujang menu items</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <span class="badge badge-success fs-6">
                                <i class="fa-solid fa-list me-1"></i><?= count($menu_items) ?> Menu Items
                            </span>
                        </div>
                    </div>

                    <!-- Menu Categories -->
                    <?php foreach ($categories as $category_name => $items): ?>
                        <div class="card mb-4">
                            <div class="category-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">
                                        <i class="fa-solid fa-<?= strtolower($category_name) === 'satay' ? 'drumstick-bite' : 'utensils' ?> me-2"></i>
                                        <?= ucwords($category_name) ?>
                                    </h4>
                                    <span class="badge badge-light"><?= count($items) ?> Items</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="row g-4">
                                    <?php foreach ($items as $item): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="card menu-card h-100 position-relative">
                                                <?php if ($item['status'] === 'available'): ?>
                                                    <span class="badge status-badge bg-success">Available</span>
                                                <?php else: ?>
                                                    <span class="badge status-badge bg-danger">Out of Stock</span>
                                                <?php endif; ?>
                                                
                                                <img src="<?= htmlspecialchars($item['image']) ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="card-img-top menu-image" 
                                                     onerror="this.src='../assets/img/no-image.png'">
                                                
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($item['name']) ?></h5>
                                                    <p class="card-text text-muted flex-grow-1"><?= htmlspecialchars($item['description']) ?></p>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <h4 class="text-primary fw-bold mb-0">
                                                            RM <?= number_format($item['price'], 2) ?>
                                                        </h4>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary" title="Edit Item">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" title="Toggle Availability">
                                                                <i class="fa-solid fa-toggle-<?= $item['status'] === 'available' ? 'on' : 'off' ?>"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

               

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
            // Add hover effects and interactions
            document.addEventListener('DOMContentLoaded', function() {
                const menuCards = document.querySelectorAll('.menu-card');
                menuCards.forEach(card => {
                    card.addEventListener('click', function(e) {
                        if (!e.target.closest('.btn-group')) {
                            // Toggle details or navigate to edit page
                            const itemName = this.querySelector('.card-title').textContent;
                            alert(`Selected: ${itemName}\n(Click edit button for full details)`);
                        }
                    });
                });

                // Toggle availability
                document.querySelectorAll('.btn-outline-danger').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const card = this.closest('.menu-card');
                        const statusBadge = card.querySelector('.status-badge');
                        const icon = this.querySelector('i');
                        
                        if (statusBadge.textContent.includes('Available')) {
                            statusBadge.textContent = 'Out of Stock';
                            statusBadge.className = 'badge status-badge bg-danger';
                            icon.className = 'fa-solid fa-toggle-off';
                        } else {
                            statusBadge.textContent = 'Available';
                            statusBadge.className = 'badge status-badge bg-success';
                            icon.className = 'fa-solid fa-toggle-on';
                        }
                    });
                });
            });
        </script>
    </body>
</html>