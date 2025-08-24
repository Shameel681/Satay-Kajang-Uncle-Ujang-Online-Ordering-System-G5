<?php
require_once 'connect.php';

$message = '';
$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpass = $_POST['password'];

    // Hash password
    $hashed = password_hash($newpass, PASSWORD_DEFAULT);

    $sql = "UPDATE customer SET password=? WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed, $email);
    if ($stmt->execute()) {
        $message = "Password updated successfully! You can now <a href='login.php'>login</a>.";
    } else {
        $message = "Failed to update password.";
    }
}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/register.css">
    <link rel="stylesheet" href="CSS/reset_pass.css">
    <link rel="stylesheet" href="CSS/login.css">

</head>
<body>
    <main>
    <section class="reset-form">
        <div class="container">
            <h2>Reset Password</h2>
            <p>Please enter your new password below.</p>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Reset Password</button>
            </form>

            <p class="back-link">
                <a href="login.php">← Back to Login</a>
            </p>
        </div>
    </section>
</main>

    <script src="script/toast.js"></script>

    
</body>
</html>
