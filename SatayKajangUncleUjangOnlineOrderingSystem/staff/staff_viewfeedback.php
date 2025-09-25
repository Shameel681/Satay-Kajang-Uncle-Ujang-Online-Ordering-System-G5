<?php
// staff/staff_viewfeedback.php
require_once '../connect.php';

// Check if staff is logged in
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_dashboard.php");
    exit;
}

// Handle Delete Feedback
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];

    if ($type === 'customer') {
        $stmt = $conn->prepare("DELETE FROM feedback_customer WHERE id=?");
        $stmt->bind_param("i", $id);
    } elseif ($type === 'guest') {
        $stmt = $conn->prepare("DELETE FROM feedback_guest WHERE id=?");
        $stmt->bind_param("i", $id);
    }
    if (isset($stmt)) {
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch Customer Feedback
$customer_feedback = $conn->query("SELECT * FROM feedback_customer ORDER BY created_at DESC");

// Fetch Guest Feedback
$guest_feedback = $conn->query("SELECT * FROM feedback_guest ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../CSS/admin_viewfeedback.css">
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
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
            <li><a href="staff_managecustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
            <li><a href="staff_menu.php"><i class="fa-solid fa-users"></i> Manage Menu</a></i></li>
            <li><a href="staff_viewfeedback.php" class="active"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>

<div class="container">
    <h1 class="mb-4">Customer Feedback</h1>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Feedback</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        while ($row = $customer_feedback->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                <td><?= htmlspecialchars($row['customer_email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['feedback'])) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>&type=customer" 
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this feedback?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h1 class="mt-5 mb-4">Guest Feedback</h1>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>ID</th>
                <th>Guest Name</th>
                <th>Email</th>
                <th>Feedback</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        while ($row = $guest_feedback->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['guest_name']) ?></td>
                <td><?= htmlspecialchars($row['guest_email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['feedback'])) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>&type=guest" 
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this feedback?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.getElementById("staffDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});

</script>
</body>
</html>