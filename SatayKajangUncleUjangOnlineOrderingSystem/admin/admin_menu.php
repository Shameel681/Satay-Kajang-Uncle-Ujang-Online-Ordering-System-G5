<?php
// admin/admin_viewfeedback.php
require_once '../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: profAdmin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Feedback</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="../CSS/admin_menu.css">
<link rel="stylesheet" href="../CSS/admin_dashboard.css">
<link rel="stylesheet" href="../CSS/profCust.css">
</head>


<div class="dashboard-wrapper">

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
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="admincustomer.php"><i class="fa-solid fa-users"></i> Manage Customer</a></li>
               <li><a href="adminstaff.php"><i class="fa-solid fa-utensils"></i> Manage Staff</a></li>
            <li><a href="admin_menu.php" class="active"><i class="fa-solid fa-utensils"></i> View Menu</a></li>
            <li><a href="admin_viewfeedback.php"><i class="fa-solid fa-comments"></i> View Feedback</a></li>
        </ul>
    </aside>

<body>
    <div class="menu-container">
        <h2 class="section-title">Satay</h2>
        <div class="menu-item">
            <img src="../image/satay ayam.png" alt="Satay Ayam" class="item-image">
            <div class="item-details">
                <div class="item-name">Satay Ayam</div>
                <div class="item-description">Ayam diperap rempah rahsia, memanggang harum semerbak</div>
            </div>
            <div class="item-price">RM 1.00</div>
        </div>
        <div class="menu-item">
            <img src="../image/satay daging.jpg" alt="Satay Daging" class="item-image">
            <div class="item-details">
                <div class="item-name">Satay Daging</div>
                <div class="item-description">Daging dihiris halus, lembut dan penuh rasa</div>
            </div>
            <div class="item-price">RM 1.20</div>
        </div>
        <div class="menu-item">
            <img src="../image/satay perut.jpg" alt="Satay Perut" class="item-image">
            <div class="item-details">
                <div class="item-name">Satay Perut</div>
                <div class="item-description">Perut direndam rempah, kenyal dan berperisa unik</div>
            </div>
            <div class="item-price">RM 1.20</div>
        </div>
        <div class="menu-item">
            <img src="../image/Satay kambing.jpg" alt="Satay Kambing" class="item-image">
            <div class="item-details">
                <div class="item-name">Satay Kambing</div>
                <div class="item-description">Kambing dipanggang tepat, wangi dan tiada bau</div>
            </div>
            <div class="item-price">RM 2.00</div>
        </div>

        <h2 class="section-title">Sides</h2>
        <div class="menu-item">
            <img src="../image/Kuah kacang.jpg" alt="Kuah Kacang" class="item-image">
            <div class="item-details">
                <div class="item-name">Kuah Kacang</div>
                <div class="item-description">Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat</div>
            </div>
            <div class="item-price">RM 2.00</div>
        </div>
        <div class="menu-item">
            <img src="../image/Nasi Impit lagi.jpg" alt="Nasi Impit" class="item-image">
            <div class="item-details">
                <div class="item-name">Nasi Impit</div>
                <div class="item-description">Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.</div>
            </div>
            <div class="item-price">RM 1.50</div>
        </div>
    </div>
</body>

<script>
document.getElementById("adminDropdown").addEventListener("click", function() {
    this.classList.toggle("active");
});
</script>
</body>
</html>
