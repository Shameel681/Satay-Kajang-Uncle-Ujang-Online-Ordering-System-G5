<?php
require_once 'connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $sql = "SELECT customer_id FROM customer WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Jump to reset form (simple version, no token)
        header("Location: reset_pass.php?email=" . urlencode($email));
        exit;
    } else {
        $message = "Email not found in our system.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="CSS/login.css">
    <link rel="stylesheet" href="CSS/base.css">
    <link rel="stylesheet" href="CSS/header.css">
    <link rel="stylesheet" href="CSS/footer.css">
    <link rel="stylesheet" href="CSS/forgot_pass.css">
</head>
<body>
    <div class="login-form">
        <h2>Forgot Password</h2>
        <?php if ($message): ?>
            <div class="message-box error"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Enter your email:</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
    </div>
</body>
</html>
