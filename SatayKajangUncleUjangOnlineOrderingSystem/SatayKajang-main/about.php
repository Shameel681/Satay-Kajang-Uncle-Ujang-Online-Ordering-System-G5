<?php

// Include the database connection file
require_once 'connect.php'; 

// Check if the user is logged in
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$customer_name = $is_loggedin ? htmlspecialchars($_SESSION['name']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satay Kajang Uncle Ujang - About Us</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crete+Round:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo-and-title">
                <div class="logo-circle">
                    <img src="image/LogoSataysebenarReal.png" alt="Satay Kajang Logo">
                </div>
                <h1><a href="index.php">Satay Kajang Uncle Ujang</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Menu</a></li>
                    <li><a href="about.php" class="active">About Us</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    
                    <?php if ($is_loggedin): ?>
                        <li><a href="profCust.php">Profile</a></li>
                    <li>
                        <a href="logout.php" class="btn">Logout</a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="register.php" class="btn">Register as Customer</a>
                    </li>
                    <li>
                        <a href="login.php" class="btn">Login</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="about">
            <div class="about-content">
                <h1>Setiap Hidangan Memiliki Kisahnya Tersendiri</h1>
                <p>di mana setiap perniagaan mempunyai cerita dan garis masa untuk dikongsi bersama</p>
            </div>
        </section>

        <section class="about-story">
            <section class="hero-intro">
                <h2 class="magic-title">Perjalanan Kami</h2>
                <p class="story-preamble">Cerita Kami bermula dari detik bermakna pada tahun 2016, saat Encik Mawardi menghidupkan semula resipi satay warisan keluarga yang telah lama tersimpan, menjadi titik tolak bagi Satay Kajang Uncle Ujang untuk menempa nama dan mencuri hati pelanggan dengan rasa tradisional yang penuh nostalgia.</p>
            </section>
            
            <div class="timeline" id="timeline-section">
                <div class="timeline-item" data-side="left">
                    <div class="timeline-year glow">2016</div>
                    <div class="timeline-content">
                        <h3 class="timeline-header">✨Permulaan Resipi Ditemui ✨</h3>
                        <p class="timeline-text">Setelah bertahun menjual air dan buah di pasar, hati kami terpanggil untuk memulakan sesuatu yang baru. Ayah kami, Encik Mawardi, mengeluarkan resipi satay warisan keluarga yang tersimpan rapi sejak zaman atoknya. Dengan harapan dan doa, kami memulakan perjalanan satay ini, membawa rasa nostalgia turun-temurun kepada pelanggan.</p>
                    </div>
                </div>
                
                <div class="timeline-item" data-side="right">
                    <div class="timeline-year glow">2019</div>
                    <div class="timeline-content">
                        <h3 class="timeline-header">🌟 Nama yang Bergema di Hati 🌟</h3>
                        <p class="timeline-text">Selepas empat tahun berusaha, satay kami menjadi buah mulut penduduk setempat. Gerai kecil kami kini menjadi tumpuan, dengan pelanggan setia dan baru yang datang setiap hari, terpikat dengan aroma dan rasa satay yang penuh kenangan. Nama Satay Kajang Uncle Ujang mula dikenali di serata kawasan Semenyih.</p>
                    </div>
                </div>
                
                <div class="timeline-item" data-side="left">
                    <div class="timeline-year glow">2020</div>
                    <div class="timeline-content">
                        <h3 class="timeline-header">🌀 Gelombang Dugaan Melanda 🌀</h3>
                        <p class="timeline-text">Tanpa disangka dunia dilanda wabah baharu iaitu wabak COVID-19 yang ganas. Pada Mac 2020, perniagaan kami terpaksa ditutup sepenuhnya akibat perintah kawalan pergerakan. Impian kami seolah terhenti, tetapi semangat keluarga ini kekal utuh, menanti hari untuk bangkit semula.</p>
                    </div>
                </div>
                
                <div class="timeline-item" data-side="right">
                    <div class="timeline-year glow">2022</div>
                    <div class="timeline-content">
                        <h3 class="timeline-header">🌱 Bersemi Kembali dengan Harapan 🌱</h3>
                        <p class="timeline-text">Dua tahun selepas wabak melanda, dunia mula pulih, dan kami sekeluarga bertekad untuk bangkit semula. Dengan semangat yang baru, kami menghidupkan kembali gerai satay kami, membawa kembali aroma dan rasa yang telah lama dirindui oleh pelanggan setia kami.</p>
                    </div>
                </div>
                
                <div class="timeline-item" data-side="left">
                    <div class="timeline-year glow">2025</div>
                    <div class="timeline-content">
                        <h3 class="timeline-header">🌈 Perjalanan yang Takkan Berhenti 🌈</h3>
                        <p class="timeline-text">Hari ini, Satay Kajang Uncle Ujang terus melangkah ke hadapan dengan penuh semangat, berazam untuk membawa warisan satay kami ke peringkat yang lebih tinggi. Dengan izin Allah, kami akan terus berkembang, menghadapi setiap cabaran dengan keberanian, dan mengajak anda semua untuk turut serta dalam perjalanan penuh rasa ini di gerai kami!</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="familysatay">
            <h2>Meet Our Team</h2>
            <div class="family-members">
                <div class="member">
                    <img src="image/gambarAyah.jpg" class="gambarAyah">
                    <h3>Mawardi bin Syamsyimi</h3>
                    <p>Founder & Recipe Creator</p>
                </div>
                <div class="member">
                    <img src="image/gambarIbu.jpg" class="gambarIbu">
                    <h3>Alianar binti Aliamat</h3>
                    <p>Manager</p>
                </div>
                <div class="member">
                    <img src="image/gambarFaris.jpg" class="gambarFaris">
                    <h3>Muhammad Faris bin Mawardi</h3>
                    <p>Head Chef</p>
                </div>
                <div class="member">
                    <img src="image/furqan.jpg" class="gambarFurqan">
                    <h3>Muhammad Furqan bin Mawardi</h3>
                    <p>Supervisor</p>
                </div>
                <div class="member">
                    <img src="image/gambar passport.JPG" class="gambarFikri">
                    <h3>Muhammad Fikri bin Mawardi</h3>
                    <p>Customer Service</p>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>© 2016 SATAY KAJANG UNCLE UJANG. All rights reserved.</p>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script src="scripts.js"></script>
</body>
</html>