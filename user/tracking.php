<?php 
require_once '../config/database.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user orders
$query = "
    SELECT o.*, s.nama_layanan 
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

$success = '';
if(isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pesanan - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="flex items-center space-x-2">
                        <img src="../assets/images/logo2_6R.png" alt="6R Laundry" class="w-15 h-10">
                    <span class="text-2xl font-bold text-gray-800">Laundry</span>
                </a>
                
                <div class="flex items-center space-x-4">
                    <a href="pesan.php" class="hidden md:inline-flex bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-plus mr-2"></i> Pesan Baru
                    </a>
                    <span class="text-gray-600">Hi, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                    <a href="../api/login.php?action=logout" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-12 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Saya</h1>
                <p class="text-gray-600 mb-8">Pantau status cucian Anda secara real-time</p>
                
                <?php if($success): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded alert-dismissible">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($orders->num_rows > 0): ?>
                    <div class="space-y-4">
                        <?php while($order = $orders->fetch_assoc()): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-800 mb-1">
                                                <?php echo htmlspecialchars($order['nama_layanan']); ?>
                                            </h3>
                                            <p class="text-gray-600 font-mono text-sm">
                                                Kode: <?php echo $order['kode_order']; ?>
                                            </p>
                                        </div>
                                        <div class="mt-4 md:mt-0">
                                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                                <?php 
                                                $status_labels = [
                                                    'pending' => 'Menunggu Konfirmasi',
                                                    'pickup' => 'Dijemput',
                                                    'process' => 'Diproses',
                                                    'washing' => 'Sedang Dicuci',
                                                    'drying' => 'Sedang Dikeringkan',
                                                    'ready' => 'Siap Diambil',
                                                    'delivery' => 'Dalam Pengantaran',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan'
                                                ];
                                                echo $status_labels[$order['status']] ?? $order['status'];
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid md:grid-cols-4 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-600">Berat</p>
                                            <p class="font-semibold"><?php echo $order['berat_kg']; ?> kg</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Total Harga</p>
                                            <p class="font-semibold text-indigo-600">
                                                Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Tanggal Pickup</p>
                                            <p class="font-semibold"><?php echo date('d M Y', strtotime($order['tanggal_pickup'])); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Waktu Pickup</p>
                                            <p class="font-semibold"><?php echo date('H:i', strtotime($order['waktu_pickup'])); ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex justify-between mb-2">
                                            <span class="text-sm text-gray-600">Progress</span>
                                            <span class="text-sm font-semibold text-indigo-600">
                                                <?php 
                                                $progress_map = [
                                                    'pending' => 10,
                                                    'pickup' => 25,
                                                    'process' => 40,
                                                    'washing' => 60,
                                                    'drying' => 75,
                                                    'ready' => 85,
                                                    'delivery' => 95,
                                                    'completed' => 100,
                                                    'cancelled' => 0
                                                ];
                                                $progress = $progress_map[$order['status']] ?? 0;
                                                echo $progress;
                                                ?>%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" 
                                                 style="width: <?php echo $progress; ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button onclick="viewDetails('<?php echo $order['kode_order']; ?>')" 
                                                class="flex-1 md:flex-none bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition ripple">
                                            <i class="fas fa-eye mr-2"></i> Detail
                                        </button>
                                        
                                        <?php if($order['status'] == 'completed'): ?>
                                            <button onclick="printReceipt('<?php echo $order['kode_order']; ?>')"
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if($order['status'] == 'pending'): ?>
                                            <button onclick="cancelOrder('<?php echo $order['kode_order']; ?>')"
                                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Pesanan</h3>
                        <p class="text-gray-600 mb-6">Anda belum memiliki pesanan. Mulai pesan laundry sekarang!</p>
                        <a href="pesan.php" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-plus mr-2"></i> Buat Pesanan
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Detail Modal -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Detail Pesanan</h2>
                    <button onclick="closeModal('detailModal')" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div id="modalContent" class="p-6">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        async function viewDetails(kodeOrder) {
            openModal('detailModal');
            document.getElementById('modalContent').innerHTML = '<div class="text-center py-8"><div class="loader mx-auto"></div></div>';
            
            try {
                const response = await fetch(`../api/order.php?action=detail&kode=${kodeOrder}`);
                const data = await response.json();
                
                if(data.success) {
                    const order = data.order;
                    const tracking = data.tracking;
                    
                    let trackingHtml = '';
                    tracking.forEach(track => {
                        trackingHtml += `
                            <div class="flex items-start space-x-4 pb-4">
                                <div class="bg-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold">${track.status}</p>
                                    <p class="text-sm text-gray-600">${track.keterangan}</p>
                                    <p class="text-xs text-gray-500 mt-1">${track.created_at}</p>
                                </div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('modalContent').innerHTML = `
                        <div class="space-y-6">
                            <div>
                                <h3 class="font-semibold text-lg mb-3">Informasi Pesanan</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Kode Order</p>
                                        <p class="font-semibold">${order.kode_order}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Status</p>
                                        <span class="status-badge status-${order.status}">${order.status}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Layanan</p>
                                        <p class="font-semibold">${order.nama_layanan}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Berat</p>
                                        <p class="font-semibold">${order.berat_kg} kg</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Alamat Pickup</p>
                                        <p class="font-semibold">${order.alamat_pickup}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Harga</p>
                                        <p class="font-semibold text-indigo-600">${formatRupiah(order.total_harga)}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-lg mb-3">Timeline Tracking</h3>
                                <div class="border-l-2 border-gray-200 ml-5 space-y-4">
                                    ${trackingHtml}
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch(error) {
                console.error(error);
                showToast('Gagal memuat detail pesanan', 'error');
            }
        }
        
        function cancelOrder(kodeOrder) {
            if(confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
                showLoader();
                fetch(`../api/order.php?action=cancel&kode=${kodeOrder}`)
                    .then(response => response.json())
                    .then(data => {
                        hideLoader();
                        if(data.success) {
                            showToast('Pesanan berhasil dibatalkan', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Gagal membatalkan pesanan', 'error');
                        }
                    });
            }
        }
        
        function printReceipt(kodeOrder) {
            window.open(`../api/order.php?action=print&kode=${kodeOrder}`, '_blank');
        }
    </script>
</body>
</html>