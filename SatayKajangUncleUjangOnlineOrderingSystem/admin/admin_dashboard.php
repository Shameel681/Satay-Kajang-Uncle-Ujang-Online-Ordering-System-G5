<?php
// admin_dashboard.php
require_once '../connect.php';


// Check session admin
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
if (!$is_loggedin) {
    header("Location: profAdmin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Satay Kajang Uncle Ujang</title>
  <link rel="stylesheet" href="../CSS/admin_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- Sidebar -->
    <!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header" id="adminDropdown">
        <img src="../image/LogoSataysebenarReal.png" alt="Logo">
        <h2>Admin Panel <i class="fa-solid fa-caret-down"></i></h2>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="dropdownMenu">
            <a href="profAdmin.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li><a href="admin_dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="admincustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
        <li><a href="admincustomer.php"><i class="fa-solid fa-users"></i> Manage Staff</a></li>
        <li><a href="adminmenu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a></li>
        <li><a href="adminorder.php"><i class="fa-solid fa-box"></i> Orders</a></li>
        <li><a href="adminsales.php"><i class="fa-solid fa-chart-line"></i> Sales</a></li>
    </ul>
</aside>


    <!-- Main Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Welcome, Admin!</h1>
            <p>Manage the system efficiently with the tools below.</p>
        </header>

        <section class="dashboard-cards">
            <div class="card">
                <i class="fa-solid fa-users card-icon"></i>
                <h3>Manage Customers</h3>
                <p>View, edit, or remove customer accounts.</p>
                <a href="admincustomer.php" class="btn">Go</a>
            </div>

            <div class="card">
                <i class="fa-solid fa-utensils card-icon"></i>
                <h3>Manage Staff</h3>
                <p>Add, update, or delete menu items.</p>
                <a href="adminstaff.php" class="btn">Go</a>
            </div>

            <div class="card">
                <i class="fa-solid fa-utensils card-icon"></i>
                <h3>Manage Menu</h3>
                <p>Add, update, or delete menu items.</p>
                <a href="adminmenu.php" class="btn">Go</a>
            </div>

            <div class="card">
                <i class="fa-solid fa-box card-icon"></i>
                <h3>Manage Orders</h3>
                <p>Track and manage customer orders.</p>
                <a href="adminorder.php" class="btn">Go</a>
            </div>

            <div class="card">
                <i class="fa-solid fa-chart-line card-icon"></i>
                <h3>Reports</h3>
                <p>View sales and performance reports.</p>
                <a href="adminsales.php" class="btn">Go</a>
            </div>
        </section>
    </main>

</div>

<script>
document.getElementById("adminDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});
</script>

</body>
</html>
