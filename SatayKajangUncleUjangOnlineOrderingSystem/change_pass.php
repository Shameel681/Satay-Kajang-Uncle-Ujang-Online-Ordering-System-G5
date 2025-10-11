<?php
require_once 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$message = '';
$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $newpass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newpass !== $confirm) {
        $message = "New password and confirmation do not match.";
    } else {
        $sql = "SELECT password FROM customer WHERE customer_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && password_verify($current, $row['password'])) {
            $hashed = password_hash($newpass, PASSWORD_DEFAULT);
            $sql = "UPDATE customer SET password=? WHERE customer_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $hashed, $customer_id);
            if ($stmt->execute()) {
                $message = "Password changed successfully!";
            } else {
                $message = "Error updating password.";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- ✅ Custom CSS -->
    <style>
        body {
            background-color: #fcfcfcff;
            font-family: "Poppins", sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #0b132b;
            color: #ffa500;
            text-align: center;
            font-size: 1.5rem;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-weight: 600;
        }
        .btn-custom {
            background-color: #ffa500;
            color: white;
            font-weight: 600;
            border-radius: 25px;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-custom:hover {
            background-color: #e59400;
            color: #fff;
        }
        .message-box {
            text-align: center;
            color: #fff;
            background-color: #0b132b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- 🔐 Change Password Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-lock me-2"></i>Change Password
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="message-box"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-custom w-100 mt-3">Update Password</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="./customer/profCust.php" class="text-decoration-none">
                                <i class="fa fa-arrow-left me-1"></i> Back to Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
