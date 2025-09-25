<?php
// admin_dashboard.php
require_once '../connect.php';

// Check session admin
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
$admin_name = $is_loggedin ? htmlspecialchars($_SESSION['admin_name']) : '';
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
            <li><a href="staff_dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="staff_managecustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
            <li><a href="staff_menu.php"><i class="fa-solid fa-utensils"></i> Manage Menu</a></li>
            <li><a href="staff_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>


    <!-- Main Content -->
    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Welcome, Admin!</h1> <strong><?php echo $admin_name; ?></strong>
            <p>Manage the system efficiently with the tools below.</p>
        </header>
        
        <!-- Quick Stats -->
    <section class="stats-cards">
        <div class="stat-card">
            <i class="fa-solid fa-users"></i>
            <div>
                <h3>Customers</h3>
                <p>345</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-user-tie"></i>
            <div>
                <h3>Staff</h3>
                <p>12</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-box"></i>
            <div>
                <h3>Orders</h3>
                <p>98</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-chart-line"></i>
            <div>
                <h3>Sales</h3>
                <p>RM 12,540</p>
            </div>
        </div>
    </section>

    <!-- Charts -->
    <section class="charts">
        <div class="chart-card">
            <h3>Sales Overview</h3>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>Orders Breakdown</h3>
            <canvas id="ordersChart"></canvas>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales chart
    const ctx1 = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Sales (RM)',
                data: [1200, 1900, 3000, 2500, 2800, 3500, 3000],
                borderColor: '#e67e22',
                backgroundColor: 'rgba(230, 126, 34, 0.1)',
                fill: true
            }]
        }
    });

    // Orders chart
    const ctx2 = document.getElementById('ordersChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Pending', 'Cancelled'],
            datasets: [{
                data: [70, 20, 10],
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c']
            }]
        }
    });
</script>

        

<script>
document.getElementById("adminDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});
</script>

</body>
</html>
