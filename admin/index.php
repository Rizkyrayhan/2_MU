<?php 
require_once '../config/database.php';

// Check if admin
if(!isLoggedIn() || !isAdmin()) {
    redirect('admin/login.php');
}

// Get statistics
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];
$total_revenue = $conn->query("SELECT SUM(total_harga) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;
$total_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];

// Get recent orders
$recent_orders = $conn->query("
    SELECT o.*, u.nama as customer_name, s.nama_layanan 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN services s ON o.service_id = s.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");

// Get orders by status
$orders_by_status = $conn->query("
    SELECT status, COUNT(*) as count 
    FROM orders 
    GROUP BY status
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex h-screen">
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-2 mb-8">
                    <div class="bg-indigo-600 rounded-full p-2">
                        <i class="fas fa-tshirt text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">6R Laundry</span>
                </div>
                
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center space-x-3 bg-indigo-600 px-4 py-3 rounded-lg">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Pesanan</span>
                        <?php if($pending_orders > 0): ?>
                            <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full"><?php echo $pending_orders; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="customers.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-users"></i>
                        <span>Pelanggan</span>
                    </a>
                    <a href="services.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-cog"></i>
                        <span>Layanan</span>
                    </a>
                    <a href="settings.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-sliders-h"></i>
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>
            
            <div class="absolute bottom-0 w-64 p-6 border-t border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                    <a href="../api/login.php?action=logout" class="text-red-400 hover:text-red-300">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-600">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                            <?php if($pending_orders > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">
                                    <?php echo $pending_orders; ?>
                                </span>
                            <?php endif; ?>
                        </button>
                        <div class="text-right">
                            <p class="text-sm font-semibold"><?php echo date('d F Y'); ?></p>
                            <p class="text-xs text-gray-600" id="currentTime"></p>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="p-6">
                <!-- Statistics Cards -->
                <div class="grid md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Total Pesanan</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_orders; ?></h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-arrow-up text-green-500"></i> Semua pesanan
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Pending</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $pending_orders; ?></h3>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-exclamation-circle text-yellow-500"></i> Perlu diproses
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Total Pendapatan</p>
                                <h3 class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h3>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-money-bill-wave text-green-600 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-arrow-up text-green-500"></i> Pesanan selesai
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 fade-in">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm mb-1">Total Pelanggan</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_customers; ?></h3>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-users text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-user-plus text-purple-500"></i> Pengguna terdaftar
                        </p>
                    </div>
                </div>
                
                <!-- Orders by Status -->
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Pesanan Terbaru</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-3 px-2 text-gray-600 font-semibold">Kode</th>
                                        <th class="text-left py-3 px-2 text-gray-600 font-semibold">Pelanggan</th>
                                        <th class="text-left py-3 px-2 text-gray-600 font-semibold">Layanan</th>
                                        <th class="text-left py-3 px-2 text-gray-600 font-semibold">Status</th>
                                        <th class="text-right py-3 px-2 text-gray-600 font-semibold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order = $recent_orders->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-2 font-mono text-sm"><?php echo $order['kode_order']; ?></td>
                                            <td class="py-3 px-2"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                            <td class="py-3 px-2"><?php echo htmlspecialchars($order['nama_layanan']); ?></td>
                                            <td class="py-3 px-2">
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-2 text-right font-semibold">
                                                Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="orders.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                                Lihat Semua Pesanan <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Status Pesanan</h2>
                        <div class="space-y-4">
                            <?php $orders_by_status->data_seek(0); ?>
                            <?php while($status = $orders_by_status->fetch_assoc()): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="status-badge status-<?php echo $status['status']; ?>">
                                            <?php echo ucfirst($status['status']); ?>
                                        </span>
                                    </div>
                                    <span class="text-2xl font-bold text-gray-800"><?php echo $status['count']; ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Aksi Cepat</h2>
                    <div class="grid md:grid-cols-4 gap-4">
                        <a href="orders.php?status=pending" 
                           class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-tasks text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Proses Pesanan</p>
                                <p class="text-sm text-gray-600"><?php echo $pending_orders; ?> pending</p>
                            </div>
                        </a>
                        
                        <a href="services.php" 
                           class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-plus text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Tambah Layanan</p>
                                <p class="text-sm text-gray-600">Kelola layanan</p>
                            </div>
                        </a>
                        
                        <a href="customers.php" 
                           class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-users text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Lihat Pelanggan</p>
                                <p class="text-sm text-gray-600"><?php echo $total_customers; ?> pelanggan</p>
                            </div>
                        </a>
                        
                        <a href="settings.php" 
                           class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:border-indigo-500 hover:shadow-md transition">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-cog text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Pengaturan</p>
                                <p class="text-sm text-gray-600">Konfigurasi sistem</p>
                            </div>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            document.getElementById('currentTime').textContent = timeString;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>