<?php
// edit_customer.php
require_once '../connect.php';
session_start();

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admincustomer.php");
    exit;
}

$customer_id = intval($_GET['id']);

// Fetch data customer
$sql = "SELECT user_id, name, email, phone, address, status FROM users WHERE user_id = ? AND role = 'customer'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "Customer not found!";
    exit;
}

// Update customer bila submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $status = $_POST['status'];

    $update = "UPDATE users SET name=?, email=?, phone=?, address=?, status=? WHERE user_id=?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("sssssi", $name, $email, $phone, $address, $status, $customer_id);

    if ($stmt->execute()) {
        header("Location: admincustomer.php");
        exit;
    } else {
        echo "Error updating record!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="../css/admincustomer.css">
</head>
<body>

<div class="form-container">
    <h2>Edit Customer</h2>
    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($customer['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']); ?>">

        <label>Address</label>
        <textarea name="address"><?= htmlspecialchars($customer['address']); ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="active" <?= $customer['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="inactive" <?= $customer['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
        </select>

        <div class="form-buttons">
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="admincustomer.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
