<?php
// Include the database connection file which starts the session
require_once 'connect.php'; 

// Define the $is_loggedin variable based on the session
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Check if the user is logged in
if (!$is_loggedin) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit;
}

// Assume the user's email is stored in the session upon login
$customer_email = $_SESSION['email'];

// SQL query to fetch the user's data from the customer table with the correct column names
$sql = "SELECT name, email, phone_no, address FROM customer WHERE email = ?";

// Use a prepared statement to prevent SQL injection
if ($stmt = $conn->prepare($sql)) {
    // Bind the customer's email to the parameter
    $stmt->bind_param("s", $customer_email);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();
    
    // Check if a user was found
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user_data = $result->fetch_assoc();
        $customer_name = htmlspecialchars($user_data['name']);
        $customer_phone = htmlspecialchars($user_data['phone_no']); // Use the correct column name
        $customer_address = htmlspecialchars($user_data['address']);
    } else {
        // This case should not happen if login is successful, but it's good practice to handle it
        $customer_name = "Not found";
        $customer_phone = "Not found";
        $customer_address = "Not found";
    }
    
    // Close the statement
    $stmt->close();
} else {
    // Handle prepare error
    die("Error preparing statement: " . $conn->error);
}

// Close the database connection
$conn->close();

// Get the current page name for active link styling
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/dashboard.css">
    <link rel="stylesheet" href="CSS/profCust.css">
    <link rel="stylesheet" href="CSS/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Anton&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
      <header class="header1">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
    </header>

    <header class="header2">
            
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" <?php if ($current_page == 'index.php') echo 'class="active"'; ?>>Home</a></li>
                    <li><a href="menu.php" <?php if ($current_page == 'menu.php') echo 'class="active"'; ?>>Menu</a></li>
                    <li><a href="about.php" <?php if ($current_page == 'about.php') echo 'class="active"'; ?>>About Us</a></li>
                    <li><a href="contact.php" <?php if ($current_page == 'contact.php') echo 'class="active"'; ?>>Contact Us</a></li>
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php" <?php if ($current_page == 'profCust.php') echo 'class="active"'; ?>>Profile</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="auth-links">
                    <?php if ($is_loggedin): ?>
                        <li><a href="logout.php" class="btn">Logout</a></li>
                    <?php else: ?>
                        <li><a href="register.php" class="btn">Register as Customer</a></li>
                        <li><a href="login.php" class="btn">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
    </header>

   

    <main>
        <section class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <i class="fas fa-user-circle profile-icon"></i>
                    <h2>Customer Profile</h2>
                </div>
                
                <div id="profileDetails">
                    <div class="profile-details">
                        <p><strong>Name:</strong> <?php echo $customer_name; ?></p>
                        <p><strong>Email:</strong> <?php echo $customer_email; ?></p>
                        <p><strong>Phone:</strong> <?php echo $customer_phone; ?></p>
                        <p><strong>Address:</strong> <?php echo $customer_address; ?></p>
                    </div>
                    <div class="profile-actions">
                        <button id="editProfileBtn" class="btn">Edit Profile</button>
                    </div>
                </div>

                <form id="editProfileForm" style="display:none;" action="update_profile.php" method="POST">
                    <div class="form-group">
                        <label for="editName">Name</label>
                        <input type="text" id="editName" name="name" value="<?php echo $customer_name; ?>">
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" id="editEmail" name="email" value="<?php echo $customer_email; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editPhone">Phone</label>
                        <input type="tel" id="editPhone" name="phone" value="<?php echo $customer_phone; ?>">
                    </div>
                    <div class="form-group">
                        <label for="editAddress">Address</label>
                        <textarea id="editAddress" name="address"><?php echo $customer_address; ?></textarea>
                    </div>
                    <div class="profile-actions">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" id="cancelEditBtn" class="btn">Cancel</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>© 2016 SATAY KAJANG UNCLE UJANG. All rights reserved.</p>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>
    <script src="script/dashboard.js"></script>
    <script src="script/profCust.js"></script>
    <script src="scripts.js"></script>
</body>
</html>