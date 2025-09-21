<?php
require_once '../connect.php';

// Check staff login
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}

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
        $message = "Failed to delete menu item.";
        $message_type = "error";
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
        $message_type = "error";
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
            $message = "Failed to add menu item.";
            $message_type = "error";
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
    $image_path = '';

    if (!empty($_FILES['image_path']['name'])) {
        $target_dir = "../image/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["image_path"]["name"]);
        if (move_uploaded_file($_FILES["image_path"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (empty($food_name) || empty($price) || empty($description) || empty($category) || empty($image_path)) {
        $message = "All fields (name, price, description, category, picture) are required for update.";
        $message_type = "error";
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
            $message = "Failed to update menu item.";
            $message_type = "error";
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
<title>Staff - Manage Menu</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../CSS/admin_menu.css">
<link rel="stylesheet" href="../CSS/staff_menu.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo-and-title">
            <div class="logo-circle">
                <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
            </div>
            <h1><a href="staff_dashboard.php">Staff Panel</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="staff_dashboard.php">Dashboard</a></li>
                <li><a href="staff_managecustomer.php">Manage Customer</a></li>
                <li><a href="staff_manageorder.php">Manage Order</a></li>
                <li><a href="staff_menu.php" class="active">Manage Menu</a></li>
                <li><a href="staff_viewfeedback.php">View Feedback</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">
    <section class="view-menu mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Menu List</h2>
            <button id="toggleFormBtn" class="btn btn-custom-register">Add New Menu</button>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th><th>Category</th><th>Name</th><th>Picture</th>
                    <th>Price (RM)</th><th>Description</th>
                    <th>Created At</th><th>Updated At</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($menu_list && $menu_list->num_rows > 0): ?>
                    <?php while ($menu = $menu_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $menu['food_id']; ?></td>
                        <td><?php echo htmlspecialchars($menu['category']); ?></td>
                        <td><?php echo htmlspecialchars($menu['food_name']); ?></td>
                        <td><img src="<?php echo $menu['image_path']; ?>" alt="<?php echo htmlspecialchars($menu['food_name']); ?>" style="max-width:80px;"></td>
                        <td><?php echo number_format($menu['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($menu['description']); ?></td>
                        <td><?php echo $menu['created_at']; ?></td>
                        <td><?php echo $menu['updated_at']; ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button 
                                type="button" 
                                class="btn btn-sm btn-warning editBtn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editMenuModal"
                                data-id="<?php echo $menu['food_id']; ?>"
                                data-name="<?php echo htmlspecialchars($menu['food_name']); ?>"
                                data-price="<?php echo $menu['price']; ?>"
                                data-desc="<?php echo htmlspecialchars($menu['description']); ?>"
                                data-cat="<?php echo $menu['category']; ?>"
                                data-img="<?php echo $menu['image_path']; ?>"
                            >Edit</button>

                            <!-- Delete -->
                            <a href="staff_menu.php?delete_id=<?php echo $menu['food_id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this menu item?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">No menu items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- ===== Add Menu Form (Hidden by default) ===== -->
    <section class="add-menu" id="addMenuForm" style="display:none;">
        <h2>Add New Menu</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="add_menu" value="1">
            <div class="form-group mb-3">
                <label>Category:</label>
                <select name="category" required class="form-control">
                    <option value="">-- Select Category --</option>
                    <option value="Main Dish">Main Dish</option>
                    <option value="Side Dish">Side Dish</option>
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Food Name:</label>
                <input type="text" name="food_name" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Price (RM):</label>
                <input type="number" name="price" step="0.01" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label>Description:</label>
                <textarea name="description" rows="3" class="form-control" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label>Image:</label>
                <input type="file" name="image_path" accept="image/*" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Add Menu</button>
        </form>
    </section>
</main>

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="edit_menu" value="1">
        <input type="hidden" name="food_id" id="edit_food_id">

        <div class="modal-header">
          <h5 class="modal-title" id="editMenuLabel">Edit Menu Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label>Category:</label>
            <select name="category" id="edit_category" class="form-control" required>
              <option value="Main Dish">Main Dish</option>
              <option value="Side Dish">Side Dish</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Food Name:</label>
            <input type="text" name="food_name" id="edit_food_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Price (RM):</label>
            <input type="number" name="price" id="edit_price" step="0.01" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" id="edit_description" rows="3" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label>Upload New Image (required):</label><br>
            <img id="edit_preview" src="" alt="Preview" style="max-width:100px;"><br><br>
            <input type="file" name="image_path" accept="image/*" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('toggleFormBtn').addEventListener('click', function() {
    var form = document.getElementById('addMenuForm');
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
});

// Fill edit modal with row data
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_food_id').value = this.dataset.id;
        document.getElementById('edit_food_name').value = this.dataset.name;
        document.getElementById('edit_price').value = this.dataset.price;
        document.getElementById('edit_description').value = this.dataset.desc;
        document.getElementById('edit_category').value = this.dataset.cat;
        document.getElementById('edit_preview').src = this.dataset.img;
    });
});
</script>

</body>
</html>
