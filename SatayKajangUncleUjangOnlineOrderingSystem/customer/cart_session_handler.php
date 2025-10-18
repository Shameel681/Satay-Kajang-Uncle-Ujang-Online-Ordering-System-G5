<?php
session_start();

header('Content-Type: application/json');

// Handle berdasarkan request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Ambil cart dari session
    echo json_encode(['cart' => $_SESSION['cart'] ?? []]);
}

elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan cart ke session
    $data = json_decode(file_get_contents("php://input"), true);
    $_SESSION['cart'] = $data['cart'] ?? [];
    echo json_encode(['status' => 'success']);
} 

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Clear cart dari session
    unset($_SESSION['cart']);
    echo json_encode(['status' => 'success']);
} 

else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}


?>