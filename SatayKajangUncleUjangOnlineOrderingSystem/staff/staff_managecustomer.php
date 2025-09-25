<?php
// staff_managecustomer.php
require_once '../connect.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: profStaff.php");
    exit;
}
$staff_name = htmlspecialchars($_SESSION['staff_name']);

// Initialize error message
$error = "";

// Handle Edit Customer
if (isset($_POST['edit_customer'])) {
    $id = $_POST['customer_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone_no']);
    $address = trim($_POST['address']);

    if (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
        $error = "Phone number must be 10 or 11 digits only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE customer SET name=?, email=?, phone_no=?, address=?, updated_at=NOW() WHERE customer_id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Delete Customer
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all customers
$result = $conn->query("SELECT * FROM customer ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="../CSS/admin_dashboard.css"> <!-- guna css sama mcm dashboard -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header" id="staffDropdown">
            <img src="../image/LogoSataysebenarReal.png" alt="Logo">
            <h2>Staff Panel <i class="fa-solid fa-caret-down"></i></h2>

            <!-- Dropdown Menu -->
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="profStaff.php"><i class="fa-solid fa-user"></i> Profile</a>
                <a href="staff_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="staff_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="staff_managecustomer.php" class="active"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
            <li><a href="staff_menu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a></li>
            <li><a href="staff_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Manage Customers</h1>
            <p>Welcome, <strong><?php echo $staff_name; ?></strong></p>
        </header>

        <div class="container">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone No</th>
                        <th>Address</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['customer_id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone_no']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['customer_id'] ?>">Edit</button>
                            <a href="?delete=<?= $row['customer_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this customer?')">Delete</a>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['customer_id'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="POST">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Customer</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="customer_id" value="<?= $row['customer_id'] ?>">
                              <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Phone No</label>
                                <input type="text" name="phone_no" class="form-control" value="<?= htmlspecialchars($row['phone_no']) ?>" pattern="[0-9]{10,11}" title="Enter 10 or 11 digit phone number" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control"><?= htmlspecialchars($row['address']) ?></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" name="edit_customer" class="btn btn-success">Save Changes</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("staffDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});
</script>
</body>
</html>
