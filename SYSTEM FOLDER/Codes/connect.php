<?php

// Pastikan sesi dimulakan jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===============================================
// TETAPAN PANGKALAN DATA (DATABASE SETTINGS)
// Kredensial ini digunakan untuk LOCALHOST (XAMPP/WAMP)
// ===============================================



 // KREDENSIAL LIVE SERVER ANDA (KOMENKAN SEMENTARA PENGUJIAN NGROK)
 $servername= "localhost";
 $username= "u134600246_satayujanguser";
 $password= "Fikrimawardi123_";
 $dbname= "u134600246_satayujangdb";



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