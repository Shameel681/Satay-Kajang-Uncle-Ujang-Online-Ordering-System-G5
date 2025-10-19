<?php

// Pastikan sesi dimulakan jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===============================================
// TETAPAN PANGKALAN DATA (DATABASE SETTINGS)
// Kredensial ini digunakan untuk LOCALHOST (XAMPP/WAMP)
// ===============================================

$servername = "localhost";
$username = "root";   // Pengguna default XAMPP/WAMP
$password = "";       // Kata laluan default XAMPP/WAMP (biarkan kosong)
$dbname = "skuuoos"; // Nama database anda di XAMPP

/* // KREDENSIAL LIVE SERVER ANDA (KOMENKAN SEMENTARA PENGUJIAN NGROK)
// $servername_live = "localhost";
// $username_live = "u134600246_satayujanguser";
// $password_live = "Fikrimawardi123_";
// $dbname_live = "u134600246_satayujangdb";
*/


// ===============================================
// SAMBUNGAN DATABASE
// ===============================================

// Cipta sambungan ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Semak sambungan
if ($conn->connect_error) {
    // Jika sambungan gagal, hentikan skrip dan paparkan ralat
    die("Connection failed: " . $conn->connect_error);
}

// Sambungan berjaya!
?>