<?php
session_start();
require_once '../connect.php'; 

// --- CONFIGURATIONS TOYYIBPAY (SILA GANTI NILAI INI) ---
// ⚠️ PENTING: Secret Key API dari dashboard ToyyibPay
$secret_key = 'epepkahf-9ets-r608-u0sh-y1vmjvq89mtm';
// Bill ID/Category Code yang diambil dari dashboard anda
$bill_id = 'xcu9w5q4'; // Ini adalah Bill ID sebenar anda
// --- AKHIR CONFIGURATIONS TOYYIBPAY ---

// Anda perlu kelas/fungsi untuk penghantaran e-mel di sini
// Contoh: require 'send_email_function.php';

if (!isset($_SESSION['customer_id']) || !isset($_GET['order_id'])) {
    header("Location: ../login.php");
    exit;
}

// Tukar kepada camelCase
$orderId = intval($_GET['order_id']);
$customerId = $_SESSION['customer_id'];

// --- FUNGSI UTAMA: MENDAPATKAN STATUS TERKINI DARI DB ---

/**
 * Mengambil butiran order dan pelanggan dari database.
 * @param mysqli $conn Objek sambungan database.
 * @param int $orderId ID Order.
 * @param int $customerId ID Pelanggan.
 * @return array|null Butiran order atau null jika tidak ditemui.
 */
function fetchOrderStatus($conn, $orderId, $customerId) {
    // 1. Ambil detail order dari DB
    $sql = "SELECT 
                o.*, 
                c.email AS customer_email, 
                c.phone_no AS customer_phone
            FROM orders o
            JOIN customer c ON o.customer_id = c.customer_id
            WHERE o.order_id = ? AND o.customer_id = ?";

    $stmt = $conn->prepare($sql);
    
    // PHPMD FIX: Menggantikan die() dengan error_log() dan return null
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

// --- FUNGSI TOYYIBPAY API: SEMAK STATUS BIL (MANUAL CHECK) ---

/**
 * Memanggil API ToyyibPay untuk mendapatkan status pembayaran bil.
 * @param string $billCode Kod Bil ToyyibPay.
 * @param string $secretKey Kunci rahsia API anda.
 * @return array|null Respons API yang telah didekodkan, atau null jika gagal.
 */
function checkToyyibPayStatusAPI($billCode, $secretKey) {
    // Guna URL Dev/Sandbox untuk testing
    $url = 'https://dev.toyyibpay.com/index.php/api/getBillTransactions'; 
    // Guna URL production: $url = 'https://toyyibpay.com/index.php/api/getBillTransactions';

    $data = [
        'billCode' => $billCode,
        'secretKey' => $secretKey,
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Matikan SSL VERIFY untuk Dev/Localhost (Hidupkan dalam Production!)

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("cURL Error for ToyyibPay Status Check: " . $err);
        return null;
    }
    
    // ToyyibPay API mengembalikan JSON array
    $decodedResponse = json_decode($response, true);

    if (isset($decodedResponse[0]) && is_array($decodedResponse[0])) {
        return $decodedResponse[0]; // Ambil transaksi pertama
    }
    return null;
}

// --- LOGIK UTAMA SCRIPT ---

$order = fetchOrderStatus($conn, $orderId, $customerId); 

if (!$order) {
    echo "<script>alert('Order tidak sah, tiada akses, atau ralat pangkalan data berlaku.'); window.location.href='menu.php';</script>";
    exit;
}

// --- PENGENDALIAN E-MEL (Jika user klik 'Email Resit') ---
$email_message = '';
if (isset($_GET['action']) && $_GET['action'] == 'email_receipt' && $order['payment_status'] == 'Paid') {
    // Logik e-mel dikekalkan sebagai placeholder.
    $email_message = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4' role='alert'>
                        <strong class='font-bold'>Peringatan:</strong>
                        <span class='block sm:inline'> Sistem e-mel belum diimplementasikan sepenuhnya. Resit telah dihantar ke " . htmlspecialchars($order['customer_email']) . " (Dummy).</span>
                      </div>";
}

// --- PENGENDALIAN SEMAKAN STATUS TOYYIBPAY (MANUAL) ---
$check_status_message = '';
if (isset($_GET['action']) && $_GET['action'] == 'check_status' && $order['payment_status'] == 'Pending') {
    $billCode = $order['bill_code'];
    $apiResponse = checkToyyibPayStatusAPI($billCode, $secret_key);

    if ($apiResponse) {
        // billStatus: 1=Success (Paid), 2=Pending, 3=Failed
        // FIX: Menggunakan operator null coalescing (?? 0) untuk mengelakkan ralat 'Undefined array key "billstatus"'
        $paymentStatus = $apiResponse['billstatus'] ?? 0; 
        $transactionId = $apiResponse['transaction_id'] ?? null;

        if ($paymentStatus == 1) {
            // STATUS PAID DISAHKAN, KEMASKINI DATABASE
            $update_sql = "UPDATE orders SET payment_status = 'Paid', transaction_id = ? WHERE order_id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {
                // Gunakan billCode jika transactionId null (sesetengah API ToyyibPay mengembalikan billCode sebagai ID transaksi)
                $finalTransactionId = $transactionId ?? $billCode; 
                $update_stmt->bind_param("si", $finalTransactionId, $orderId);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect untuk memuatkan status baharu dari DB dan memaparkan butang resit
                header("Location: view_order_stat_cust.php?order_id={$orderId}");
                exit;
            } else {
                 $check_status_message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                            <strong class='font-bold'>Ralat DB:</strong>
                                            <span class='block sm:inline'> Gagal mengemas kini pangkalan data tempatan. Sila hubungi sokongan.</span>
                                          </div>";
            }
        } elseif ($paymentStatus == 2) {
            $check_status_message = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                        <strong class='font-bold'>Status Semakan:</strong>
                                        <span class='block sm:inline'> Status pembayaran masih Pending di ToyyibPay.</span>
                                      </div>";
        } elseif ($paymentStatus == 3) {
            $check_status_message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                        <strong class='font-bold'>Status Semakan:</strong>
                                        <span class='block sm:inline'> Pembayaran telah Gagal di ToyyibPay.</span>
                                      </div>";
        } else {
             // Ini termasuk kes $paymentStatus = 0 (ralat API/status tidak diketahui)
             $check_status_message = "<div class='bg-gray-100 border border-gray-400 text-gray-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                        <strong class='font-bold'>Status Semakan:</strong>
                                        <span class='block sm:inline'> Status tidak diketahui. Sila semak ToyyibPay secara manual atau hubungi sokongan.</span>
                                      </div>";
        }

    } else {
        $check_status_message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4' role='alert'>
                                    <strong class='font-bold'>Ralat API:</strong>
                                    <span class='block sm:inline'> Gagal menghubungi ToyyibPay API untuk semakan status.</span>
                                  </div>";
    }
    // Dapatkan semula order (untuk memaparkan status yang mungkin baru dikemaskini)
    $order = fetchOrderStatus($conn, $orderId, $customerId); 
}


// 2. Ambil butiran item order
$items_sql = "SELECT 
                oi.quantity, 
                oi.price_each AS item_price,
                p.food_name
              FROM order_items oi
              JOIN menu p ON oi.food_id = p.food_id
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);

// PEMERIKSAAN Ralat: Jika prepare gagal kerana nama jadual/kolum salah
if ($items_stmt === false) {
    $error_msg = 'SQL Error semasa fetching item order: ' . htmlspecialchars($conn->error) . '. Query Gagal: ' . htmlspecialchars($items_sql);
    $conn->close();
    echo "<!DOCTYPE html><title>Error</title><body style='font-family: Inter, sans-serif; background-color: #fee2e2; padding: 20px;'>
            <h1 style='color: #991b1b;'>Ralat Pangkalan Data Kritikal</h1>
            <p style='color: #4b5563; font-weight: bold;'>Sila semak nama jadual/kolum anda:</p>
            <pre style='background-color: #fca5a5; padding: 15px; border-radius: 8px; white-space: pre-wrap; word-wrap: break-word;'>{$error_msg}</pre>
          </body>";
    exit;
}

$items_stmt->bind_param("i", $orderId); // Guna $orderId
$items_stmt->execute();
$order_items = $items_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$items_stmt->close();
$conn->close();


?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Order #<?php echo $orderId; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fc; }
        .card { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); }
        .status-paid { background-color: #D1FAE5; color: #065F46; font-weight: 600; padding: 4px 8px; border-radius: 6px; }
        .status-pending { background-color: #FEF3C7; color: #B45309; font-weight: 600; padding: 4px 8px; border-radius: 6px; }
        .status-failed { background-color: #FEE2E2; color: #991B1B; font-weight: 600; padding: 4px 8px; border-radius: 6px; }
    </style>
</head>
<body class="p-4 sm:p-8 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-xl">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Status Order Anda</h1>

        <?php echo $email_message; // Papar mesej e-mel ?>
        <?php echo $check_status_message; // Papar mesej semakan status ?>


        <!-- KOTAK STATUS UTAMA -->
        <div class="card bg-white p-6 rounded-xl border border-gray-200 mb-6 text-center">
            <?php if ($order['payment_status'] == 'Paid'): ?>
                <span class="text-6xl mb-4 block">✅</span>
                <p class="text-xl font-semibold text-green-700 mb-2">Pembayaran Berjaya Disahkan!</p>
                <p class="text-gray-600">Terima kasih atas pembayaran anda. Order anda kini sedang **Processing**.</p>
            <?php elseif ($order['payment_status'] == 'Pending'): ?>
                <span class="text-6xl mb-4 block">⏳</span>
                <p class="text-xl font-semibold text-yellow-700 mb-2">Pembayaran Masih Belum Selesai.</p>
                <p class="text-gray-600">Sila semak status pembayaran di ToyyibPay atau cuba semula. <br>
                    <span class="text-sm font-semibold">Kod Bil ToyyibPay: <span class="font-mono"><?php echo htmlspecialchars($order['bill_code']); ?></span>.
                    <!-- Anda boleh letak butang "Bayar di sini" jika perlu -->
                </p>
            <?php else: ?>
                 <span class="text-6xl mb-4 block">❌</span>
                 <p class="text-xl font-semibold text-red-700 mb-2">Pembayaran Gagal/Dibatalkan.</p>
                 <p class="text-gray-600">Sila cuba buat pembayaran semula untuk order ini.</p>
            <?php endif; ?>
        </div>

        <!-- RINGKASAN PESANAN -->
        <div class="card bg-white p-6 rounded-xl border border-gray-200 mb-6">
            <h2 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Ringkasan Pesanan #<?php echo $orderId; ?></h2>

            <div class="space-y-3">
                <div class="flex justify-between items-center text-gray-700">
                    <span class="font-semibold">Status Pembayaran:</span>
                    <span class="
                        <?php 
                        if ($order['payment_status'] == 'Paid') echo 'status-paid';
                        elseif ($order['payment_status'] == 'Pending') echo 'status-pending';
                        else echo 'status-failed';
                        ?>
                    "><?php echo htmlspecialchars($order['payment_status']); ?></span>
                </div>
                <div class="flex justify-between items-center text-gray-700">
                    <span class="font-semibold">Status Order:</span>
                    <span class="px-2 py-1 bg-gray-100 rounded-lg text-sm"><?php echo htmlspecialchars($order['order_status']); ?></span>
                </div>
                <div class="flex justify-between items-center text-gray-700">
                    <span class="font-semibold">Jumlah Bayaran:</span>
                    <span class="text-lg font-bold text-indigo-700">RM <?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <?php if ($order['transaction_id']): ?>
                <div class="flex justify-between items-center text-gray-700 text-sm">
                    <span class="font-semibold">ID Transaksi (ToyyibPay):</span>
                    <span class="font-mono break-all"><?php echo htmlspecialchars($order['transaction_id']); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <h3 class="font-semibold text-gray-800 mt-6 mb-3 border-t pt-4">Item Dipesan:</h3>
            <?php $subtotal = 0; ?>
            <?php foreach ($order_items as $item): ?>
                <?php $item_total = $item['item_price'] * $item['quantity']; $subtotal += $item_total; ?>
                <div class="flex justify-between text-sm text-gray-600">
                    <!-- Menggunakan p.food_name yang telah diambil -->
                    <span><?php echo htmlspecialchars($item['food_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>RM <?php echo number_format($item_total, 2); ?></span>
                </div>
            <?php endforeach; ?>

        </div>

        <!-- KOTAK TINDAKAN (Butang Resit / Bayar Semula / Semak Status) -->
        <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            
            <?php if ($order['payment_status'] == 'Paid'): ?>
                <!-- Butang 1: LIHAT/DOWNLOAD RESIT -->
                <button 
                    onclick="window.open('generate_receipt.php?order_id=<?php echo $orderId; ?>', '_blank')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 flex items-center justify-center">
                    Lihat/Download Resit 🧾
                </button>
                
                <!-- Butang 2: EMAIL RESIT -->
                <a href="?order_id=<?php echo $orderId; ?>&action=email_receipt"
                    class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-purple-500 focus:ring-opacity-50 flex items-center justify-center">
                    Email Resit 📧
                </a>
            <?php elseif ($order['payment_status'] == 'Pending'): ?>
                <!-- Butang 1: SEMAK STATUS TERKINI -->
                <a href="?order_id=<?php echo $orderId; ?>&action=check_status"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50 flex items-center justify-center">
                    Semak Status Terkini 📡
                </a>
                
                <!-- Butang 2: CUBA BAYAR SEMULA -->
                <form method="POST" action="process_payment.php" class="w-full sm:w-auto">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50">
                        Cuba Bayar Semula 🔄
                    </button>
                </form>
            <?php else: ?>
                <!-- Butang untuk cuba bayar semula (Jika Gagal) -->
                <form method="POST" action="process_payment.php" class="w-full sm:w-auto">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-xl transition duration-150 ease-in-out shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-orange-500 focus:ring-opacity-50">
                        Cuba Bayar Semula 🔄
                    </button>
                </form>
            <?php endif; ?>
            
            <a href="menu.php" class="w-full sm:w-auto bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-xl text-center transition duration-150 ease-in-out shadow-md">
                Balik ke Menu Utama
            </a>
        </div>
    </div>

</body>
</html>
