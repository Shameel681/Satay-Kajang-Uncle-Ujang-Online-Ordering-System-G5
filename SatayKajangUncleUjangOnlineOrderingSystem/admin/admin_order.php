<?php

require_once '../connect.php';

// ✅ Pastikan hanya admin boleh akses
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Ambil data order dari database
$sql = "SELECT o.order_id, c.name AS customer_name, o.total_price, o.status, o.created_at 
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/admin_order.css"> <!-- ✅ External CSS -->
</head>
<body>
<div class="container my-5">
    <h2 class="text-center mb-4"><i class="fa-solid fa-receipt"></i> Manage Orders</h2>
    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 order-card">
                        <h5><i class="fa-solid fa-user"></i> <?= htmlspecialchars($row['customer_name']) ?></h5>
                        <p><i class="fa-solid fa-hashtag"></i> <strong>Order ID:</strong> <?= $row['order_id'] ?></p>
                        <p><i class="fa-solid fa-dollar-sign"></i> <strong>Total:</strong> RM <?= number_format($row['total_price'], 2) ?></p>
                        <p><i class="fa-solid fa-calendar"></i> <strong>Date:</strong> <?= $row['created_at'] ?></p>
                        <span class="status-badge 
                            <?= $row['status'] == 'pending' ? 'status-pending' : 
                               ($row['status'] == 'processing' ? 'status-processing' : 
                               ($row['status'] == 'completed' ? 'status-completed' : 'status-cancelled')) ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="view_order.php?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                            <a href="update_order.php?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i> Update
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fa-solid fa-circle-info"></i> No orders found.
        </div>
    <?php endif; ?>
</div>
</body>
</html>
