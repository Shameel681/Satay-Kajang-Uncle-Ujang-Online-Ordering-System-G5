<?php
// admin/admin_viewfeedback.php
require_once '../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: profAdmin.php");
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
<link rel="stylesheet" href="../CSS/admin_viewfeedback.css">
</head>
<body>

<header>
    <div class="container">
        <div class="logo-and-title">
            <div class="logo-circle">
                <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
            </div>
            <h1><a href="admin_dashboard.php">Admin Panel</a></h1>
        </div>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admincustomer.php">Manage Customer</a></li>
                <li><a href="adminstaff.php">Manage Staff</a></li>
                <li><a href="manageadmin.php">Manage Admin</a></li>
                <li><a href="admin_menu.php">View Menu</a></li>
                <li><a href="admin_viewfeedback.php" class="active">View Feedback</a></li>
            </ul>
        </nav>
    </div>
</header>

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
</body>
</html>
