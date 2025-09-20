<?php
// Include database connection + session
require_once '../connect.php';

// Check if the admin is logged in
$is_loggedin = isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
$admin_name = $is_loggedin ? htmlspecialchars($_SESSION['admin_name']) : '';

// If not logged in, redirect to login
if (!$is_loggedin) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu - Satay Kajang Uncle Ujang</title>
    <link rel="stylesheet" href="../CSS/admin_menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" 
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    <main>
        <section class="menu">
            <div class="container">
                <h2>Admin View - Menu</h2>
                <p>Welcome, <strong><?php echo $admin_name; ?></strong>. Here’s the current customer menu:</p>
                
                <!-- Include the same menu items as customer menu -->
                <div class="menu-category">
                    <h3>Satay</h3>
                    <ul>
                        <li>
                            <img src="../image/satay ayam.png" alt="Satay Ayam" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Ayam <span class="price">RM 1.00</span></h4>
                                <p>Ayam diperap rempah rahsia, memanggang harum semerbak</p>
                            </div>
                        </li>
                        <li>
                            <img src="../image/satay daging.jpg" alt="Satay Daging" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Daging <span class="price">RM 1.20</span></h4>
                                <p>Daging dihiris halus, lembut dan penuh rasa</p>
                            </div>
                        </li>
                        <li>
                            <img src="../image/satay perut.jpg" alt="Satay Perut" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Perut <span class="price">RM 1.20</span></h4>
                                <p>Perut direndam rempah, kenyal dan berperisa unik</p>
                            </div>
                        </li>
                        <li>
                            <img src="../image/Satay kambing.jpg" alt="Satay Kambing" class="menu-image">
                            <div class="menu-details">
                                <h4>Satay Kambing <span class="price">RM 2.00</span></h4>
                                <p>Kambing dipanggang tepat, wangi dan tiada bau</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="menu-category">
                    <h3>Sides</h3>
                    <ul>
                        <li>
                            <img src="../image/Kuah kacang.jpg" alt="Kuah Kacang" class="menu-image">
                            <div class="menu-details">
                                <h4>Kuah Kacang <span class="price">RM 2.00</span></h4>
                                <p>Kuah kacang yang dimasak sempurna, memberikan rasa lemak-manis yang memikat</p>
                            </div>
                        </li>
                        <li>
                            <img src="../image/Nasi Impit lagi.jpg" alt="Nasi Impit" class="menu-image">
                            <div class="menu-details">
                                <h4>Nasi Impit <span class="price">RM 1.50</span></h4>
                                <p>Nasi impit padat tapi lembut, dikukus segar setiap pagi untuk tekstur sempurna ketika dicicah dengan kuah.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <!-- tambah nnti -->
    </footer>
</body>
</html>
