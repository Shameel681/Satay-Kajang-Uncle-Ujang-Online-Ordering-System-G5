<?php
session_start();
require_once '../connect.php';

// Masukkan library PHPMailer
// **PASTIKAN LOKASI FAIL INI ADALAH BETUL BERDASARKAN STRUKTUR PROJEK ANDA**
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Laluan yang sedia ada dalam kod anda:
require '../vendor/phpmailer/phpmailer/src/Exception.php'; // Baris 10
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';


// Tetapkan pembolehubah $is_loggedin di sini
$is_loggedin = isset($_SESSION['customer_id']);

// --- CONFIGURATIONS TOYYIBPAY (SILA GANTI NILAI INI) ---
// Secret Key API dari dashboard ToyyibPay
$secret_key = 'epepkahf-9ets-r608-u0sh-y1vmjvq89mtm';
// Bill ID/Category Code yang diambil dari dashboard anda
$bill_id = 'xcu9w5q4'; // Ini adalah Bill ID sebenar anda
// --- AKHIR CONFIGURATIONS TOYYIBPAY ---

if (!$is_loggedin || !isset($_GET['order_id'])) {
    // Jika tiada order_id dalam URL, cuba gunakan order_id dari sesi
    if ($is_loggedin && isset($_SESSION['last_viewed_order_id'])) {
        // Redirect ke URL dengan Order ID yang disimpan dalam sesi
        header("Location: view_order_stat_cust.php?order_id=" . $_SESSION['last_viewed_order_id']);
        exit;
    }
    
    // Jika masih tiada Order ID (dalam URL atau Sesi), arahkan ke senarai order (atau login)
    header("Location: ../login.php"); 
    exit;
}

// Tukar kepada camelCase
$orderId = intval($_GET['order_id']);
$customerId = $_SESSION['customer_id'];

// --- FUNGSI UTAMA: MENDAPATKAN STATUS TERKINI DARI DB (Tiada perubahan pada fungsi) ---

/**
 * Mengambil butiran order dan pelanggan dari database.
 */
function fetchOrderStatus($conn, $orderId, $customerId) {
    // 1. Ambil detail order dari DB
    $sql = "SELECT 
                o.*, 
                c.email AS customer_email, 
                c.phone_no AS customer_phone,
                c.email AS customer_name_for_receipt 
            FROM orders o
            JOIN customer c ON o.customer_id = c.customer_id
            WHERE o.order_id = ? AND o.customer_id = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        error_log('SQL Prepare Error in fetchOrderStatus: ' . $conn->error . ' Query: ' . $sql);
        return null; 
    }

    $stmt->bind_param("ii", $orderId, $customerId); 
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        return null; // Order tidak ditemui atau bukan milik pengguna
    }

    return $order;
}

/**
 * Memanggil API ToyyibPay untuk mendapatkan status pembayaran bil.
 */
function checkToyyibPayStatusAPI($billCode, $secretKey) {
    $url = 'https://toyyibpay.com/index.php/api/getBillTransactions'; 

    $data = [
        'billCode' => $billCode,
        'secretKey' => $secretKey,
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("cURL Error for ToyyibPay Status Check: " . $err);
        return null;
    }
    
    $decodedResponse = json_decode($response, true);

    if (isset($decodedResponse[0]) && is_array($decodedResponse[0])) {
        return $decodedResponse[0]; // Ambil transaksi pertama
    }
    return null;
}


// --- FUNGSI PENGHANTARAN E-MEL BARU MENGGUNAKAN PHPMailer ---

/**
 * Menghantar resit HTML sebagai kandungan e-mel (inline).
 * @return bool True jika berjaya, False jika gagal.
 */
function sendReceiptEmail($recipientEmail, $orderId, $receiptHtml) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // GANTI dengan Host SMTP anda (e.g., mail.yourdomain.com)
        $mail->SMTPAuth = true;
        $mail->Username = 'toonpow43@gmail.com'; // GANTI dengan Emel anda
        $mail->Password = 'mzyp uzsq aarf mmmq'; // GANTI dengan Kata Laluan Aplikasi/Emel anda
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Gunakan PHPMailer::ENCRYPTION_STARTTLS jika port 587
        $mail->Port = 465; // GANTI dengan port SMTP anda (587 atau 465)

        // Penerima
        $mail->setFrom('no-reply@uncle-ujang.com', 'Satay Kajang Uncle Ujang');
        $mail->addAddress($recipientEmail);

        // Kandungan
        $mail->isHTML(true);
        $mail->Subject = 'Resit Pesanan Anda #' . $orderId . ' - Satay Kajang Uncle Ujang';
        
        // Kandungan Badan E-mel (HTML dan Teks Ringkas)
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                    .greeting { font-size: 1.1em; color: #333; }
                    .receipt-content { margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 5px solid #007bff; }
                    .footer { margin-top: 20px; font-size: 0.9em; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <p class='greeting'>Hai Pelanggan,</p>
                    <p>Terima kasih kerana memilih Satay Kajang Uncle Ujang! Kami lampirkan butiran resit pesanan anda di bawah ini.</p>
                    
                    <div class='receipt-content'>
                        " . $receiptHtml . "
                    </div>

                    <p style='margin-top: 20px;'>Jika anda mempunyai sebarang pertanyaan, sila hubungi kami.</p>
                    <p class='footer'>Hormat kami,<br>Pasukan Satay Kajang Uncle Ujang</p>
                </div>
            </body>
            </html>
        ";

        $mail->Body = $body;
        $mail->AltBody = "Resit Pesanan Anda #" . $orderId . ". Sila lihat versi HTML untuk butiran penuh.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Catat ralat terperinci
        error_log("Gagal menghantar e-mel. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}


// --- FUNGSI MENJANA HTML RESIT (Tiada perubahan pada fungsi ini) ---
// (Fungsi generateReceiptHtml dikekalkan seperti kod asal anda, hanya sebahagian kecil yang dipadam untuk brevity)
/**
 * Menjana output HTML yang direka sebagai resit.
 */
function generateReceiptHtml($order, $order_items) {
    if (!is_array($order_items)) {
        $order_items = []; 
    }

    // Mengambil Order ID untuk URL Kembali yang betul
    $orderId = htmlspecialchars($order['order_id']);

    // NOTA: Saya keluarkan tag <html>, <head>, <body>, dan butang Cetak/Kembali dari sini 
    // kerana resit ini akan dimasukkan ke dalam body e-mel, bukan paparan skrin penuh.
    // Jika anda mahu ia kekal sebagai fail lampiran, anda perlukan library PDF.
    // Untuk tujuan ini, saya kekalkan gaya resit sebagai inline HTML dalam body e-mel.
    
    $html = '
    <div style="font-family: sans-serif; max-width: 380px; margin: 0 auto;">
        <div style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px;">
            <h2 style="margin: 5px 0; font-size: 1.5em;">Satay Kajang Uncle Ujang</h2>
            <p style="margin: 3px 0; font-size: 0.9em;">Jalan Satay, Taman Impian, Kajang, Selangor</p>
            <p style="margin: 3px 0; font-size: 0.9em;">Tel: N/A | Email: N/A</p>
        </div>

        <div style="margin-bottom: 15px;">
            <p style="margin: 3px 0; font-size: 0.9em;"><strong>Resit Jualan</strong></p>
            <p style="margin: 3px 0; font-size: 0.9em;">Order ID: <strong>#' . $orderId . '</strong></p>
            <p style="margin: 3px 0; font-size: 0.9em;">Tarikh: ' . date('d M Y, h:i A', strtotime($order['order_date'])) . '</p>
            <p style="margin: 3px 0; font-size: 0.9em;">Pelanggan: ' . htmlspecialchars($order['customer_name_for_receipt'] ?? 'N/A') . '</p>
            <p style="margin: 3px 0; font-size: 0.9em;">Status Pembayaran: <span style="color: green; font-weight: bold;">' . htmlspecialchars($order['payment_status'] ?? 'N/A') . '</span></p>';
    if (!empty($order['transaction_id'])) {
        $html .= '<p style="margin: 3px 0; font-size: 0.9em;">ID Transaksi: <small>' . htmlspecialchars($order['transaction_id']) . '</small></p>';
    }
    $html .= '</div>

        <div style="margin-bottom: 15px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9em;">
                <thead>
                    <tr>
                        <th style="padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: left; width: 60%;">Item</th>
                        <th style="padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: center; width: 15%;">Qty</th>
                        <th style="padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: right; width: 25%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>';
    
    foreach ($order_items as $item) {
        $item_total = $item['item_price'] * $item['quantity'];
        $html .= '<tr>
                        <td style="padding: 5px 0; border-bottom: 1px dashed #ccc;">' . htmlspecialchars($item['food_name']) . '</td>
                        <td style="padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: center;">' . htmlspecialchars($item['quantity']) . '</td>
                        <td style="padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: right;">RM ' . number_format($item_total, 2) . '</td>
                    </tr>';
    }

    $html .= '</tbody>
            </table>
        </div>

        <div style="margin-bottom: 15px;">
            <div style="font-size: 1.1em; font-weight: bold; padding-top: 10px; border-top: 2px solid #333; display: flex; justify-content: space-between;">
                <span>JUMLAH AKHIR:</span>
                <span>RM ' . number_format($order['total_amount'] ?? 0, 2) . '</span>
            </div>
            <p style="text-align: right; font-size: 0.8em; margin-top: 5px;">* Harga termasuk SST (jika berkenaan)</p>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 0.9em;">
            <p>Kami menghargai pesanan anda!</p>
        </div>
    </div>';

    return $html;
}


// --- LOGIK UTAMA SCRIPT ---

$order = fetchOrderStatus($conn, $orderId, $customerId); 

if (!$order) {
    // ... (Logik pengendalian order tidak sah)
    echo "<script>alert('Order tidak sah, tiada akses, atau ralat pangkalan data berlaku.'); window.location.href='menu.php';</script>";
    
    if (isset($_SESSION['last_viewed_order_id'])) {
        unset($_SESSION['last_viewed_order_id']);
    }
    exit;
}

// Tambahkan Logik Sesi
if ($order) {
    // Simpan order ID ini dalam sesi untuk kegunaan navbar
    $_SESSION['last_viewed_order_id'] = $orderId;

    // Tambahan: Jika status order sudah selesai, buang ID dari sesi 
    if (in_array($order['order_status'], ['Completed', 'Picked Up', 'Delivered'])) {
        unset($_SESSION['last_viewed_order_id']);
    }
}
// Akhir Tambahan Logik Sesi

// 2. Ambil butiran item order
$items_sql = "SELECT 
                oi.quantity, 
                oi.price_each AS item_price,
                p.food_name
              FROM order_items oi
              JOIN menu p ON oi.food_id = p.food_id
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);

$order_items = []; // Isytiharkan di sini

if ($items_stmt) {
    $items_stmt->bind_param("i", $orderId); 
    $items_stmt->execute();
    $result = $items_stmt->get_result();
    
    if ($result) {
        $order_items = $result->fetch_all(MYSQLI_ASSOC);
    }
    $items_stmt->close();
} else {
    // Pengendalian ralat SQL jika prepare gagal
    error_log('SQL Error semasa fetching item order: ' . $conn->error);
}


// --- PENGENDALIAN E-MEL (Jika user klik 'Email Resit') ---
$email_message = '';
if (isset($_GET['action']) && $_GET['action'] == 'email_receipt' && $order['payment_status'] == 'Paid') {
    // Menjana Resit HTML untuk dimasukkan ke dalam body e-mel
    $receipt_html_content = generateReceiptHtml($order, $order_items);
    
    // Panggil fungsi penghantaran e-mel
    if (sendReceiptEmail($order['customer_email'], $orderId, $receipt_html_content)) {
        $email_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            <strong>Berjaya!</strong> Resit Pesanan #{$orderId} telah dihantar ke emel: " . htmlspecialchars($order['customer_email']) . ".
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
    } else {
        $email_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                            <strong>Gagal!</strong> Gagal menghantar resit emel. Sila semak log server untuk ralat SMTP.
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
    }
}

// --- PENGENDALIAN GENERATE/DOWNLOAD RESIT KEKAL SAMA ---
if (isset($_GET['action']) && $_GET['action'] == 'download_receipt' && $order['payment_status'] == 'Paid') {
    // Ini kekal dengan versi skrin penuh yang sedia ada
    function generateFullReceiptHtml($order, $order_items) {
        // ... (Kod penuh anda di sini untuk paparan cetak) ...
        // Untuk ringkas, saya biarkan fungsi ini memanggil yang asal dan menyertakan tag HTML penuh
        $internal_receipt = generateReceiptHtml($order, $order_items);
        
        // Membungkus resit dalam template cetakan asal anda
        // NOTA: Saya mengembalikan template HTML penuh yang asal (seperti yang anda berikan sebelum ini)
        // untuk memastikan fungsi Cetak / Muat Turun tidak terjejas.
        $orderId = htmlspecialchars($order['order_id']);

        return '<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resit Pesanan #' . $orderId . '</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .receipt-container { max-width: 400px; margin: 0 auto; background-color: white; padding: 20px; border: 1px dashed #ccc; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 5px 0; font-size: 1.5em; }
        .details, .items, .footer { margin-bottom: 15px; }
        .details p { margin: 3px 0; font-size: 0.9em; }
        .items table { width: 100%; border-collapse: collapse; font-size: 0.9em; }
        .items th, .items td { padding: 5px 0; border-bottom: 1px dashed #ccc; text-align: left; }
        .items th:nth-child(2), .items td:nth-child(2) { text-align: center; }
        .items th:nth-child(3), .items td:nth-child(3) { text-align: right; }
        .total-row { font-size: 1.1em; font-weight: bold; padding-top: 10px; border-top: 2px solid #333; }
        .thank-you { text-align: center; margin-top: 20px; font-size: 0.9em; }
        @media print {
            body { background-color: white; }
            .receipt-container { border: none; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        ' . str_replace(['<!DOCTYPE html>', '<html>', '<body>', '</html>', '</body>', '<head>', '</head>', '<style>', '</style>'], '', $internal_receipt) . '
    </div>
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #3b82f6; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak Resit</button>
        
        <a href="view_order_stat_cust.php?order_id=' . $orderId . '" 
           style="text-decoration: none; padding: 10px 20px; background-color: #f3f4f6; color: #374151; border: 1px solid #ccc; border-radius: 5px; cursor: pointer; margin-left: 10px; display: inline-block;">
            Kembali
        </a>
        
    </div>
</body>
</html>';
    }

    $receipt_html = generateFullReceiptHtml($order, $order_items);
    echo $receipt_html;
    exit; 
}


// --- PENGENDALIAN SEMAKAN STATUS TOYYIBPAY (MANUAL) KEKAL SAMA ---
$check_status_message = '';
// ... (Logik Semakan ToyyibPay kekal sama) ...
if (isset($_GET['action']) && $_GET['action'] == 'check_status' && $order['payment_status'] == 'Pending') {
    $billCode = $order['bill_code'];
    $apiResponse = checkToyyibPayStatusAPI($billCode, $secret_key);

    if ($apiResponse) {
        $paymentStatus = $apiResponse['billstatus'] ?? 0; 
        $transactionId = $apiResponse['transaction_id'] ?? null;

        if ($paymentStatus == 1) {
            $update_sql = "UPDATE orders SET payment_status = 'Paid', transaction_id = ? WHERE order_id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {
                $finalTransactionId = $transactionId ?? $billCode; 
                $update_stmt->bind_param("si", $finalTransactionId, $orderId);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect selepas berjaya, memastikan sesi dikemaskini.
                header("Location: view_order_stat_cust.php?order_id={$orderId}");
                exit;
            } else {
               $check_status_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                            <strong class='font-bold'>Ralat DB:</strong> Gagal mengemas kini pangkalan data tempatan. Sila hubungi sokongan.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                          </div>";
            }
        } elseif ($paymentStatus == 2) {
            $check_status_message = "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                            <strong class='font-bold'>Status Semakan:</strong> Status pembayaran masih Pending di ToyyibPay.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                          </div>";
        } elseif ($paymentStatus == 3) {
            $check_status_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                            <strong class='font-bold'>Status Semakan:</strong> Pembayaran telah Gagal di ToyyibPay.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                          </div>";
        } else {
               $check_status_message = "<div class='alert alert-secondary alert-dismissible fade show' role='alert'>
                                            <strong class='font-bold'>Status Semakan:</strong> Status tidak diketahui. Sila semak ToyyibPay secara manual atau hubungi sokongan.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                          </div>";
        }

    } else {
        $check_status_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                            <strong class='font-bold'>Ralat API:</strong> Gagal menghubungi ToyyibPay API untuk semakan status.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                          </div>";
    }
    // Dapatkan semula order (jika status dikemaskini)
    $order = fetchOrderStatus($conn, $orderId, $customerId); 
}

// Tutup sambungan DB di akhir logik umum, tetapi sebelum HTML output
$conn->close();

// Dapatkan nama pelanggan dari email (sebagai placeholder)
$customer_name = htmlspecialchars(explode('@', $order['customer_email'])[0] ?? 'Pelanggan');

// Fungsi pembantu untuk menentukan kelas Badge
function getPaymentStatusBadgeClass($status) {
    switch ($status) {
        case 'Paid': return 'badge bg-success';
        case 'Pending': return 'badge bg-warning text-dark';
        case 'Failed': return 'badge bg-danger';
        default: return 'badge bg-secondary';
    }
}

function getOrderStatusBadgeClass($status) {
    switch ($status) {
        case 'Completed':
        case 'Delivered':
        case 'Picked Up':
            return 'badge bg-primary';
        case 'Processing':
        case 'Ready for Pickup':
            return 'badge bg-info text-dark';
        default:
            return 'badge bg-secondary';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Satay Kajang Uncle Ujang - Order Status</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Satay Kajang Uncle Ujang, Malaysian satay, Kajang restaurant" name="keywords">
    <meta content="Learn about Satay Kajang Uncle Ujang, the best place for authentic Malaysian satay in Kajang." name="description">

    <link href="../img/favicon.ico" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../lib/animate/animate.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <link href="../CSS/bootstrap.min.css" rel="stylesheet">

    <link href="../CSS/styles.css" rel="stylesheet">

    <style>
        .navbar-brand img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }

        /* Gaya tambahan untuk status order */
        .order-status-icon {
            font-size: 4rem; /* Saiz ikon */
            line-height: 1;
        }

        /* Pastikan main content tidak 'stuck' ke kiri */
        .main-content-wrapper {
            display: flex;
            justify-content: center;
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
    </style>
</head>
<body>
    <div class="container-xxl bg-white p-0">
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="../index.php" class="navbar-brand p-0">
                    <h1 class="text-primary m-0">
                        <img src="../image/LogoSataysebenarReal.png" alt="Satay Kajang Logo"><small>Satay Kajang Uncle Ujang</small></h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        <a href="../index.php" class="nav-item nav-link">Home</a>
                        <a href="menu.php" class="nav-item nav-link">Menu</a>
                        <a href="about.php" class="nav-item nav-link">About Us</a>
                        <a href="contact.php" class="nav-item nav-link">Contact Us</a>
                        <?php if ($is_loggedin): ?>
                        <a href="view_order_stat_cust.php" class="nav-item nav-link active">Order Status</a>
                        <?php endif?>
                    </div>
                    <?php if ($is_loggedin): ?>
                        <a href="profCust.php" class="btn btn-primary py-2 px-4">Profile</a>
                    <?php else: ?>
                        <a href="../register.php" class="btn btn-primary py-2 px-4 mx-2">Register</a>
                        <a href="../login.php" class="btn btn-primary py-2 px-4 mx-2">Login</a>
                    <?php endif; ?>
                </div>
            </nav>


<div class="container-xxl py-5 bg-dark hero-header mb-5">
                <div class="container text-center my-5 pt-5 pb-4">
                    <h1 class="display-3 text-white mb-3 animated slideInDown">Order Status</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center text-uppercase">
                            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                            <li class="breadcrumb-item text-white active" aria-current="page">Order Status</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="container main-content-wrapper">
    <div class="col-lg-8 col-md-10 col-sm-12">
        <h2 class="section-title ff-secondary text-center text-primary fw-normal mb-5">Status Order Anda</h2>

        <?php echo $email_message; ?>
        <?php echo $check_status_message; ?>

        <div class="card shadow-lg mb-5 p-4 text-center border-0">
            <div class="card-body">
                <?php if (($order['payment_status'] ?? '') == 'Paid'): ?>
                    <i class="fa fa-check-circle text-success order-status-icon mb-3"></i>
                    <h3 class="card-title text-success mb-2">Pembayaran Berjaya Disahkan!</h3>
                    <p class="card-text text-muted">Terima kasih atas pembayaran anda. Order anda kini sedang **Processing**.</p>
                <?php elseif (($order['payment_status'] ?? '') == 'Pending'): ?>
                    <i class="fa fa-clock text-warning order-status-icon mb-3"></i>
                    <h3 class="card-title text-warning mb-2">Pembayaran Masih Belum Selesai.</h3>
                    <p class="card-text text-muted">Sila semak status pembayaran di ToyyibPay atau cuba semula. <br>
                        <small class="fw-bold">Kod Bil ToyyibPay: <span class="font-monospace"><?php echo htmlspecialchars($order['bill_code'] ?? 'N/A'); ?></span></small>
                    </p>
                <?php else: ?>
                    <i class="fa fa-times-circle text-danger order-status-icon mb-3"></i>
                    <h3 class="card-title text-danger mb-2">Pembayaran Gagal/Dibatalkan.</h3>
                    <p class="card-text text-muted">Sila cuba buat pembayaran semula untuk order ini.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Ringkasan Pesanan #<?php echo $orderId; ?></h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-money-bill-alt text-primary me-2"></i> <strong>Status Pembayaran:</strong></span>
                        <span class="<?php echo getPaymentStatusBadgeClass($order['payment_status'] ?? ''); ?>"><?php echo htmlspecialchars($order['payment_status'] ?? 'N/A'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-truck text-primary me-2"></i> <strong>Status Order:</strong></span>
                        <span class="<?php echo getOrderStatusBadgeClass($order['order_status'] ?? ''); ?>"><?php echo htmlspecialchars($order['order_status'] ?? 'N/A'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span><i class="fa fa-tag text-primary me-2"></i> <strong>Jumlah Bayaran:</strong></span>
                        <span class="fs-5 fw-bold text-primary">RM <?php echo number_format($order['total_amount'] ?? 0, 2); ?></span>
                    </li>
                    <?php if (!empty($order['transaction_id'])): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center small text-muted">
                        <span>ID Transaksi:</span>
                        <span class="font-monospace text-break"><?php echo htmlspecialchars($order['transaction_id'] ?? $order['bill_code'] ?? 'N/A'); ?></span>
                    </li>
                    <?php endif; ?>
                </ul>

                <h5 class="fw-bold border-bottom pb-2 mb-3">Item Dipesan:</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center" style="width: 15%;">Qty</th>
                                <th class="text-end" style="width: 20%;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $subtotal = 0; ?>
                            <?php foreach ($order_items as $item): ?>
                                <?php $item_total = $item['item_price'] * $item['quantity']; $subtotal += $item_total; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['food_name']); ?></td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end">RM <?php echo number_format($item_total, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between pt-3 border-top border-secondary">
                    <h5 class="fw-bold text-dark">Total Bayaran Akhir:</h5>
                    <h5 class="fw-bold text-dark">RM <?php echo number_format($order['total_amount'] ?? $subtotal, 2); ?></h5>
                </div>
            </div>
        </div>

        <div class="d-grid gap-3 mb-5">
            <?php if (($order['payment_status'] ?? '') == 'Paid'): ?>
                <a href="view_order_stat_cust.php?order_id=<?php echo $orderId; ?>&action=download_receipt" 
                    target="_blank" 
                    class="btn btn-success py-3 px-4">
                    <i class="fa fa-receipt me-2"></i> Muat Turun / Cetak Resit
                </a>
                
                <a href="view_order_stat_cust.php?order_id=<?php echo $orderId; ?>&action=email_receipt" 
                    class="btn btn-info py-3 px-4">
                    <i class="fa fa-envelope me-2"></i> Hantar Resit Ke Emel
                </a>

            <?php elseif (($order['payment_status'] ?? '') == 'Pending'): ?>
                <a href="view_order_stat_cust.php?order_id=<?php echo $orderId; ?>&action=check_status" class="btn btn-warning py-3 px-4 text-dark">
                    <i class="fa fa-sync-alt me-2"></i> Semak Status Pembayaran (ToyyibPay)
                </a>
                <a href="https://toyyibpay.com/<?php echo htmlspecialchars($order['bill_code'] ?? ''); ?>" 
                    target="_blank" 
                    class="btn btn-primary py-3 px-4">
                    <i class="fa fa-credit-card me-2"></i> Bayar Sekarang
                </a>
            <?php endif; ?>

            <a href="menu.php" class="btn btn-secondary py-3 px-4 mt-3">
                <i class="fa fa-arrow-left me-2"></i> Kembali ke Menu
            </a>
        </div>
    </div>
</div>
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
                <div class="container py-5">
                    <div class="row g-5">
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Explore</h4>
                            <a class="btn btn-link" href="../index.php">Home</a>
                            <a class="btn btn-link" href="../customer/menu.php">Menu</a>
                            <a class="btn btn-link" href="../customer/about.php">About Us</a>
                            <a class="btn btn-link" href="../customer/contact.php">Contact Us</a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Contact</h4>
                            <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+6011-62226128</p>
                            <p class="mb-2"><i class="fa fa-envelope me-3"></i>toonpow43@gmail.com</p>
                            <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>1, Jalan Tps 1/6, Taman Pelangi Semenyih, 43500 Semenyih, Selangor</p>
                            <div class="d-flex pt-2">
                                <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
                                <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-instagram"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Opening Hours</h4>
                            <h5 class="text-light fw-normal">Monday - Saturday</h5>
                            <p>6.00pm - 11.00pm</p>
                            <h5 class="text-light fw-normal">Sunday</h5>
                            <p>5.00pm - 11.00pm</p>

                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Staff Portal</h4>
                            <a class="btn btn-link" href="../staff/staff_login.php">Staff Login</a>
                            <a class="btn btn-link" href="../admin/admin_login.php">Admin Login</a>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="copyright">
                        <div class="row">
                            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                &copy; <a class="border-bottom" href="#">Satay Kajang Uncle Ujang</a>, All Rights Reserved.
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/wow/wow.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/counterup/counterup.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="../js/main.js"></script>
</body>
</html>