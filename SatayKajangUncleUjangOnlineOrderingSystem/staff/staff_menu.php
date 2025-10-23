<?php
// staff_menu.php
require_once '../connect.php';


// Check staff login
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

$staff_name = isset($_SESSION['staff_name']) ? htmlspecialchars($_SESSION['staff_name']) : '';
$message = '';
$message_type = '';

// ========== DELETE MENU ==========
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM menu WHERE food_id=?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("s", $delete_id);

    if ($stmt->execute()) {
        $message = "Menu item deleted successfully.";
        $message_type = "success";
    } else {
        $message = "Failed to delete menu item: " . $stmt->error;
        $message_type = "danger";
    }
    $stmt->close();
}

// ========== ADD MENU ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_menu'])) {
    $food_name = trim($_POST['food_name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image_path = '';

    if (!empty($_FILES['image_path']['name'])) {
        $target_dir = "../image/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["image_path"]["name"]);
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (empty($food_name) || empty($price) || empty($description) || empty($category) || empty($image_path)) {
        $message = "All fields (name, price, description, category, picture) are required.";
        $message_type = "danger";
    } else {
        // Generate ID prefix based on category
        $prefix = ($category === 'Main Dish') ? 'F' : 'S';
        $id_sql = "SELECT food_id FROM menu WHERE food_id LIKE '$prefix%' ORDER BY food_id DESC LIMIT 1";
        $result = $conn->query($id_sql);
        if ($row = $result->fetch_assoc()) {
            $last_num = intval(substr($row['food_id'], 1));
            $new_id = $prefix . str_pad($last_num + 1, 2, "0", STR_PAD_LEFT);
        } else {
            $new_id = $prefix . "01";
        }

        $insert_sql = "INSERT INTO menu (food_id, food_name, price, description, image_path, category, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ssdsss", $new_id, $food_name, $price, $description, $image_path, $category);

        if ($stmt->execute()) {
            $message = "Menu item added successfully.";
            $message_type = "success";
        } else {
            $message = "Failed to add menu item: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// ========== UPDATE MENU ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['edit_menu'])) {
    $food_id = $_POST['food_id'];
    $food_name = trim($_POST['food_name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $image_path = $_POST['current_image']; // Keep existing image if no new upload

    if (!empty($_FILES['image_path']['name'])) {
        $target_dir = "../image/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["image_path"]["name"]);
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (empty($food_name) || empty($price) || empty($description) || empty($category)) {
        $message = "All fields (name, price, description, category) are required for update.";
        $message_type = "danger";
    } else {
        $update_sql = "UPDATE menu 
                       SET food_name=?, price=?, description=?, image_path=?, category=?, updated_at=NOW()
                       WHERE food_id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sdssss", $food_name, $price, $description, $image_path, $category, $food_id);

        if ($stmt->execute()) {
            $message = "Menu item updated successfully.";
            $message_type = "success";
        } else {
            $message = "Failed to update menu item: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// ========== FETCH MENU ==========
$menu_list = $conn->query("SELECT food_id, category, food_name, image_path, price, description, created_at, updated_at 
                           FROM menu ORDER BY food_id ASC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu - Satay Kajang Uncle Ujang</title>
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function () { sessionStorage.fonts = true; }
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <style>
        .table img {
            max-width: 80px;
            border-radius: 5px;
        }
        .add-menu-form {
            transition: all 0.3s ease;
        }
        .table-actions .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="staff_dashboard.php" class="logo">
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
                            <a href="staff_dashboard.php">
                                <i class="fa-solid fa-gauge"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_managecustomer.php">
                                <i class="fa-solid fa-users"></i>
                                <p>Manage Customer</p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="staff_menu.php">
                                <i class="fa-solid fa-utensils"></i>
                                <p>Manage Menu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_manageorder.php">
                                <i class="fa-solid fa-shopping-cart"></i>
                                <p>Manage Orders</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_viewfeedback.php">
                                <i class="fa-solid fa-comments"></i>
                                <p>View Feedback</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profStaff.php">
                                <i class="fa-solid fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="staff_logout.php">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="main-panel">
            <!-- Main Header -->
            <div class="main-header">
                <div class="main-header-logo">
                    <a href="staff_dashboard.php" class="logo">
                        <img src="../image/LogoSataysebenarReal.png" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
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
                                <input type="text" placeholder="Search..." class="form-control" />
                            </div>
                        </nav>
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    
                                    <span class="profile-username">
                                        <span class="op-7">Welcome,</span>
                                        <span class="fw-bold"><?php echo $staff_name; ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="profStaff.php">
                                            <i class="fa-solid fa-user me-2"></i> Profile
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="staff_logout.php">
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
                    <!-- Page Header -->
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h1 class="fw-bold mb-3">Manage Menu</h1>
                            <p class="op-7 mb-2">Add, edit, or delete menu items for Satay Kajang Uncle Ujang</p>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <button id="toggleFormBtn" class="btn btn-primary btn-round">
                                <i class="fa-solid fa-plus me-2"></i>Add New Menu
                            </button>
                        </div>
                    </div>

                    <!-- Message Alert -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Menu List -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-head-row">
                                <h4 class="card-title">
                                    <i class="fa-solid fa-utensils me-2 text-primary"></i>
                                    Menu List
                                </h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Category</th>
                                            <th>Name</th>
                                            <th>Picture</th>
                                            <th>Price (RM)</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($menu_list && $menu_list->num_rows > 0): ?>
                                            <?php while ($menu = $menu_list->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($menu['food_id']); ?></td>
                                                <td><?php echo htmlspecialchars($menu['category']); ?></td>
                                                <td><?php echo htmlspecialchars($menu['food_name']); ?></td>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($menu['image_path']); ?>" 
                                                         alt="<?php echo htmlspecialchars($menu['food_name']); ?>" 
                                                         class="img-fluid" style="max-width:80px;">
                                                </td>
                                                <td><?php echo number_format($menu['price'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($menu['description']); ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($menu['created_at'])); ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($menu['updated_at'])); ?></td>
                                                <td class="table-actions">
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-sm btn-warning editBtn" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editMenuModal"
                                                        data-id="<?php echo htmlspecialchars($menu['food_id']); ?>"
                                                        data-name="<?php echo htmlspecialchars($menu['food_name']); ?>"
                                                        data-price="<?php echo $menu['price']; ?>"
                                                        data-desc="<?php echo htmlspecialchars($menu['description']); ?>"
                                                        data-cat="<?php echo htmlspecialchars($menu['category']); ?>"
                                                        data-img="<?php echo htmlspecialchars($menu['image_path']); ?>">
                                                        <i class="fa-solid fa-edit me-1"></i>Edit
                                                    </button>
                                                    <a href="staff_menu.php?delete_id=<?php echo htmlspecialchars($menu['food_id']); ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure you want to delete this menu item?');">
                                                        <i class="fa-solid fa-trash me-1"></i>Delete
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="9" class="text-center">No menu items found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Add Menu Form (Hidden by default) -->
                    <div class="card add-menu-form" id="addMenuForm" style="display:none;">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fa-solid fa-plus-circle me-2 text-success"></i>
                                Add New Menu Item
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <input type="hidden" name="add_menu" value="1">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                        <select name="category" class="form-select" required>
                                            <option value="">-- Select Category --</option>
                                            <option value="Main Dish">Main Dish</option>
                                            <option value="Side Dish">Side Dish</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Food Name <span class="text-danger">*</span></label>
                                        <input type="text" name="food_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Price (RM) <span class="text-danger">*</span></label>
                                        <input type="number" name="price" step="0.01" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Image <span class="text-danger">*</span></label>
                                        <input type="file" name="image_path" accept="image/*" class="form-control" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                        <textarea name="description" rows="3" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" id="cancelAddBtn" class="btn btn-secondary">
                                        <i class="fa-solid fa-times me-2"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa-solid fa-plus me-2"></i>Add Menu
                                    </button>
                                </div>
                            </form>
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

        <!-- Edit Menu Modal -->
        <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="edit_menu" value="1">
                        <input type="hidden" name="food_id" id="edit_food_id">
                        <input type="hidden" name="current_image" id="edit_current_image">

                        <div class="modal-header">
                            <h5 class="modal-title" id="editMenuLabel">
                                <i class="fa-solid fa-utensils me-2"></i>Edit Menu Item
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                    <select name="category" id="edit_category" class="form-select" required>
                                        <option value="Main Dish">Main Dish</option>
                                        <option value="Side Dish">Side Dish</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Food Name <span class="text-danger">*</span></label>
                                    <input type="text" name="food_name" id="edit_food_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Price (RM) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="edit_price" step="0.01" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Image</label>
                                    <img id="edit_preview" src="" alt="Preview" class="img-fluid mb-2" style="max-width:100px;">
                                    <input type="file" name="image_path" accept="image/*" class="form-control">
                                    <div class="form-text">Leave empty to keep current image</div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="edit_description" rows="3" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Core JS Files -->
        <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
        <script src="../assets/js/core/popper.min.js"></script>
        <script src="../assets/js/core/bootstrap.min.js"></script>
        <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
        <script src="../assets/js/kaiadmin.min.js"></script>

        <script>
            // Toggle Add Menu Form
            document.getElementById('toggleFormBtn').addEventListener('click', function() {
                var form = document.getElementById('addMenuForm');
                form.style.display = (form.style.display === 'none') ? 'block' : 'none';
            });

            // Cancel Add Menu Form
            document.getElementById('cancelAddBtn').addEventListener('click', function() {
                document.getElementById('addMenuForm').style.display = 'none';
            });

            // Fill Edit Modal
            document.querySelectorAll('.editBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('edit_food_id').value = this.dataset.id;
                    document.getElementById('edit_food_name').value = this.dataset.name;
                    document.getElementById('edit_price').value = this.dataset.price;
                    document.getElementById('edit_description').value = this.dataset.desc;
                    document.getElementById('edit_category').value = this.dataset.cat;
                    document.getElementById('edit_preview').src = this.dataset.img;
                    document.getElementById('edit_current_image').value = this.dataset.img;
                });
            });

            // Auto-dismiss alerts
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        </script>
</body>
</html>