<?php
session_start();
require_once '../connect.php'; // Anggap ini fail koneksi database

// 1. Semak log masuk
if (!isset($_SESSION['customer_id'])) {
    // Pengguna tidak log masuk, redirect
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id === 0) {
    die("ID Order tidak sah.");
}

// 2. Ambil detail order (Pastikan ia PAID)
// Pastikan anda mempunyai medan 'transaction_id' dalam jadual 'orders'
$sql = "SELECT 
            o.*, 
            c.name AS customer_name,
            c.email AS customer_email
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ? AND o.customer_id = ? AND o.payment_status = 'Paid'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    // Order tidak wujud, bukan milik pelanggan ini, atau BELUM DIBAYAR
    echo "<!DOCTYPE html><html lang='ms'><head><title>Akses Ditolak</title><script src='https://cdn.tailwindcss.com'></script><style>body { font-family: 'Inter', sans-serif; }</style></head><body class='p-8'><div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'><strong class='font-bold'>Gagal!</strong><span class='block sm:inline'> Resit ini hanya tersedia selepas pembayaran penuh.</span></div></body></html>";
    exit;
}

// 3. Ambil butiran item order
$items_sql = "SELECT 
                oi.quantity, 
                oi.price AS item_price,
                p.product_name
              FROM order_items oi
              JOIN product p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
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
    <title>Resit Pembayaran - Order #<?php echo $order_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #e2e8f0; }
        .receipt-container { max-width: 600px; }
        .receipt-box { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1); }
        /* Gaya untuk pencetakan */
        @media print {
            body { background-color: white; }
            .print-hidden { display: none; }
            .receipt-container { margin: 0; max-width: none; }
            .receipt-box { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body class="p-4 sm:p-8 flex items-start justify-center">

    <div class="receipt-container w-full">
        <!-- Butang Cetak/Tutup (Sembunyi semasa cetak) -->
        <div class="text-right mb-4 print-hidden">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-xl transition duration-150 ease-in-out mr-2">
                Cetak Resit (Simpan PDF) 🖨️
            </button>
            <button onclick="window.close()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-xl transition duration-150 ease-in-out">
                Tutup ✖️
            </button>
        </div>

        <div class="receipt-box bg-white p-6 sm:p-10 rounded-xl border border-gray-200">
            
            <div class="border-b-4 border-dashed border-gray-300 pb-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800 mb-1">RESIT PEMBAYARAN</h1>
                <p class="text-sm text-gray-500">Bukti Pembayaran Penuh & Sah</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-6">
                <!-- Info Syarikat -->
                <div>
                    <p class="font-bold text-gray-700">Penjual:</p>
                    <p class="text-gray-600">Satay Kajang Uncle Ujang</p>
                    <p class="text-gray-600">support@sataykajang.com</p>
                </div>
                <!-- Info Resit -->
                <div class="text-right">
                    <p class="font-bold text-gray-700">No. Order:</p>
                    <p class="text-gray-600 font-mono text-lg">#<?php echo $order_id; ?></p>
                    <p class="font-bold text-gray-700 mt-2">Tarikh Bayaran:</p>
                    <!-- Menggunakan tarikh order kerana tiada medan tarikh bayaran -->
                    <p class="text-gray-600"><?php echo date('d M Y, H:i A', strtotime($order['order_date'])); ?></p>
                </div>
            </div>

            <!-- Info Pelanggan -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <p class="font-bold text-gray-700 mb-1">Dibayar Oleh:</p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['customer_email']); ?></p>
            </div>

            <!-- Detail Item -->
            <div class="mb-6">
                <div class="border-b border-gray-200 flex justify-between py-2 font-semibold text-gray-700">
                    <span>Item</span>
                    <span>Jumlah</span>
                </div>
                
                <?php $subtotal = 0; ?>
                <?php foreach ($order_items as $item): ?>
                <?php $item_total = $item['item_price'] * $item['quantity']; $subtotal += $item_total; ?>
                    <div class="flex justify-between py-2 border-b border-gray-100 text-gray-700 text-sm">
                        <span><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                        <span>RM <?php echo number_format($item_total, 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <!-- Subtotal and Grand Total -->
                <div class="space-y-1 mt-4 text-sm text-gray-700">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>RM <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <!-- Anggap tiada diskaun/cukai dalam contoh ini, jumlah penuh terus -->
                    <div class="flex justify-between font-bold text-lg text-gray-800 border-t pt-2 mt-2">
                        <span>JUMLAH BAYARAN:</span>
                        <span>RM <?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Detail Pembayaran -->
            <div class="p-4 bg-indigo-50 rounded-lg text-sm">
                <p class="font-bold text-indigo-700 mb-1">Maklumat Pembayaran</p>
                <div class="grid grid-cols-2 text-indigo-600">
                    <div>
                        <p>Status:</p>
                        <p>ID Transaksi:</p>
                        <p>Platform:</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-green-700">PAID (BERJAYA)</p>
                        <p class="font-mono break-all"><?php echo htmlspecialchars($order['transaction_id'] ?: 'N/A'); ?></p>
                        <p>ToyyibPay</p>
                    </div>
                </div>
            </div>
            
            <p class="text-center text-xs text-gray-400 mt-6">TERIMA KASIH atas pesanan anda. Sila simpan resit ini sebagai bukti pembayaran.</p>

        </div>
    </div>

</body>
</html>
