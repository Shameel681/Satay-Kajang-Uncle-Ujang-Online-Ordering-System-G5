<?php
session_start();
require_once '../connect.php'; 
if (!isset($_SESSION['staff_loggedin']) || $_SESSION['staff_loggedin'] !== true) {
    header("Location: staff_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Satay Kajang Uncle Ujang</title>
    <link rel="stylesheet" href="../CSS/base.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/footer.css">
    <link rel="stylesheet" href="../CSS/staff_dashboard.css">
</head>
<body>

    <header>
        <h1>Satay Kajang Uncle Ujang - Staff Dashboard</h1>
    </header>

    <nav>
        <a href="../staff/staff_dashboard.php">Dashboard</a>
        <a href="../staff/staff_orders.php">Orders</a>
        <a href="../staff/staff_customers.php">Customers</a>
        <a href="../logout.php">Logout</a>
    </nav>

    <div class="container">
        <p class="welcome">Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong> 👋</p>

        <div class="cards">
            <div class="card">
                <h3>Manage Orders</h3>
                <p>View and update customer orders.</p>
                <a href="staff_orders.php">Go</a>
            </div>
            <div class="card">
                <h3>Menu Management</h3>
                <p>Update satay items and pricing.</p>
                <a href="staff_menu.php">Go</a>
            </div>
            <div class="card">
                <h3>Reports</h3>
                <p>Check sales and performance reports.</p>
                <a href="staff_reports.php">Go</a>
            </div>
        </div>

        <a href="../logout.php" class="logout">Logout</a>
    </div>

</body>
</html>
