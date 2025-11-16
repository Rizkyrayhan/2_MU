<?php 
require_once '../config/database.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('admin/login.php');
}

// Filter
$filter_status = isset($_GET['status']) ? cleanInput($_GET['status']) : 'all';

//Get Setatus
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];

// Get orders
$query = "
    SELECT o.*, u.nama as customer_name, u.telepon, s.nama_layanan 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN services s ON o.service_id = s.id 
";

if($filter_status != 'all') {
    $query .= " WHERE o.status = '$filter_status'";
}

$query .= " ORDER BY o.created_at DESC";
$orders = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex-shrink-0">
            <div class="p-6">
                <div class="flex items-center space-x-2 mb-8">
                        <img src="../assets/images/logo2_6R.png" alt="Logo" class="w-9 h-7">
                    <span class="text-xl font-bold">Laundry</span>
                </div>
                
                <nav class="space-y-2">
                    <a href="index.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
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
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-bold text-gray-800">Kelola Pesanan</h1>
                    <p class="text-gray-600">Pantau dan kelola semua pesanan laundry</p>
                </div>
            </header>
            
            <main class="p-6">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex flex-wrap gap-2">
                        <a href="orders.php?status=all" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Semua
                        </a>
                        <a href="orders.php?status=pending" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Pending
                        </a>
                        <a href="orders.php?status=pickup" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'pickup' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Pickup
                        </a>
                        <a href="orders.php?status=process" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'process' ? 'bg-purple-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Proses
                        </a>
                        <a href="orders.php?status=ready" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'ready' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Siap
                        </a>
                        <a href="orders.php?status=completed" 
                           class="px-4 py-2 rounded-lg <?php echo $filter_status == 'completed' ? 'bg-teal-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                            Selesai
                        </a>
                    </div>
                </div>
                
                <!-- Orders Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Kode Order</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Pelanggan</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Layanan</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Berat</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Total</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Status</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Tanggal</th>
                                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($orders->num_rows > 0): ?>
                                    <?php while($order = $orders->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-4 px-6">
                                                <span class="font-mono text-sm font-semibold"><?php echo $order['kode_order']; ?></span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div>
                                                    <p class="font-semibold"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                                    <p class="text-sm text-gray-600"><?php echo $order['telepon']; ?></p>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6"><?php echo htmlspecialchars($order['nama_layanan']); ?></td>
                                            <td class="py-4 px-6"><?php echo $order['berat_kg']; ?> kg</td>
                                            <td class="py-4 px-6">
                                                <span class="font-semibold text-indigo-600">
                                                    Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="text-sm text-gray-600">
                                                    <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex justify-center space-x-2">
                                                    <button onclick="viewOrder(<?php echo $order['id']; ?>)" 
                                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition"
                                                            title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')" 
                                                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition"
                                                            title="Update Status">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-2"></i>
                                            <p>Tidak ada pesanan</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content max-w-md">
            <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
                <h2 class="text-xl font-bold">Update Status Pesanan</h2>
            </div>
            <div class="p-6">
                <form id="statusForm">
                    <input type="hidden" id="orderId" name="order_id">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Status Baru</label>
                        <select id="newStatus" name="status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="pending">Pending</option>
                            <option value="pickup">Pickup</option>
                            <option value="process">Process</option>
                            <option value="washing">Washing</option>
                            <option value="drying">Drying</option>
                            <option value="ready">Ready</option>
                            <option value="delivery">Delivery</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Tambahkan keterangan..."></textarea>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" 
                                class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-check mr-2"></i> Update
                        </button>
                        <button type="button" onclick="closeModal('statusModal')"
                                class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function viewOrder(orderId) {
            // Implement view order details
            window.location.href = `order-detail.php?id=${orderId}`;
        }
        
        function updateStatus(orderId, currentStatus) {
            document.getElementById('orderId').value = orderId;
            document.getElementById('newStatus').value = currentStatus;
            openModal('statusModal');
        }
        
        document.getElementById('statusForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {
                order_id: formData.get('order_id'),
                status: formData.get('status'),
                keterangan: formData.get('keterangan')
            };
            
            try {
                const response = await fetch('../api/order.php?action=update_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    showToast('Status berhasil diupdate', 'success');
                    closeModal('statusModal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || 'Gagal mengupdate status', 'error');
                }
            } catch(error) {
                console.error(error);
                showToast('Terjadi kesalahan', 'error');
            }
        });
    </script>
</body>
</html>