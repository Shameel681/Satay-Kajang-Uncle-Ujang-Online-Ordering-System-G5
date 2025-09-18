<?php
require_once '../connect.php';
require '../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_no = $_POST['phone_no'] ?? '';
    $password = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($name) || empty($email) || empty($phone_no) || empty($password) || empty($address)) {
        $message = "All fields are required.";
        $message_type = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
        $message_type = "error";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone_no)) {
        $message = "Phone number must be exactly 10 or 11 digits.";
        $message_type = "error";
    } else {
        // Hash password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cek email staff exist
        $check_sql = "SELECT staff_id FROM staff WHERE email=?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email is already registered for another staff.";
            $message_type = "error";
        } else {
            // Generate staff_id terakhir +1
            $id_sql = "SELECT MAX(staff_id) AS max_id FROM staff";
            $result = $conn->query($id_sql);
            $row = $result->fetch_assoc();
            $staff_id = ($row['max_id'] ?? 100) + 1;

            // Insert staff
            $insert_sql = "INSERT INTO staff (staff_id, name, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("isss", $staff_id, $name, $email, $password_hash);


            if ($stmt->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'toonpow43@gmail.com'; // ganti dengan email admin
                    $mail->Password = 'mzyp uzsq aarf mmmq';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('youremail@gmail.com', 'Satay Kajang Admin');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Staff Account Verification';
                    $mail->Body = "
                        <h3>Hi $name,</h3>
                        <p>Your staff account has been created by admin.</p>
                        <p>Staff ID: <b>$staff_id</b></p>
                        <p>Password: <b>$password</b></p>
                        <p>Please login at: <a href='http://localhost/MASTER PROJECT - Satay kajang Uncle Ujang G05/Satay-Kajang-Uncle-Ujang-Online-Ordering-System-G5/SatayKajangUncleUjangOnlineOrderingSystem/staff/staff_login.php'>Staff Login</a></p>
                        <p>After login, you may change your password.</p>
                    ";

                    $mail->send();
                    $message = "Staff added successfully! Verification email sent.";
                    $message_type = "success";
                } catch (Exception $e) {
                    $message = "Staff added but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $message_type = "error";
                }
            } else {
                $message = "Failed to add staff. Please try again.";
                $message_type = "error";
            }

            $stmt->close();
        }
        $check_stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Staff - Add Staff</title>
<link rel="stylesheet" href="../CSS/base.css">
<link rel="stylesheet" href="../CSS/header.css">
<link rel="stylesheet" href="../CSS/register.css">
<link rel="stylesheet" href="../CSS/login.css">


</head>
<body>
<header>
    <div class="container">
        <h1>Admin Panel - Add Staff</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="adminstaff.php" class="active">Manage Staff</a></li>
                <li><a href="profAdmin.php">Profile</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
<section class="add-staff">
    <div class="container">
        <h2>Add New Staff</h2>
        <?php if (!empty($message)): ?>
            <div class="message-box <?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone_no" pattern="[0-9]{10,11}" title="Phone number must be 10-11 digits" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" minlength="8" required>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <input type="text" name="address" required>
            </div>
            <button type="submit" class="btn">Add Staff</button>
        </form>
    </div>
</section>
</main>


</body>
</html>
