<?php
// admincustomer.php
require_once '../connect.php';

// Check session admin
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch semua customer dari DB
$sql = "SELECT user_id, name, email, phone, address, status, created_at FROM users WHERE role = 'customer'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="../css/admincustomer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
    <h1>Manage Customers</h1>
    <table class="customer-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['user_id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                    <td><?= htmlspecialchars($row['address']); ?></td>
                    <td>
                        <span class="status <?= $row['status'] === 'active' ? 'active' : 'inactive'; ?>">
                            <?= ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td><?= date("d M Y", strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="editcust_admin.php?id=<?= $row['user_id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="8">No customers found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
