<?php
session_start();
include("db.php");

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $query = "SELECT * FROM customer WHERE reset_token='$token' AND reset_expiry > NOW()";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        if (isset($_POST['submit'])) {
            $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update = "UPDATE customer SET password='$newPass', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'";
            mysqli_query($conn, $update);
            $_SESSION['msg'] = "Password berjaya ditukar. Sila login semula.";
            header("Location: login.php");
            exit;
        }
    } else {
        echo "Token tidak sah atau telah tamat tempoh.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="POST">
        <label>Password Baru:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit" name="submit">Simpan Password</button>
    </form>
</body>
</html>
