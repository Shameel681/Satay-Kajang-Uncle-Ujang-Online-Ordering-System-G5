<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    // Localhost (testing)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "skuuoos";
} else {
    // Online (Hostinger)
    $servername = "localhost";
    $username = "u134600246_satayujanguser";
    $password = "Fikrimawardi123_";
    $dbname = "u134600246_satayujangdb";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
