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
<link rel="stylesheet" href="../CSS/admin_menu.css">
</head>


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
</body>
</html>
