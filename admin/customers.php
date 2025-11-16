<?php 
require_once '../config/database.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('admin/login.php');
}

//Get Setatus
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];

// Get all customers
$customers = $conn->query("
    SELECT u.*, 
           COUNT(o.id) as total_orders,
           SUM(CASE WHEN o.status = 'completed' THEN o.total_harga ELSE 0 END) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.role = 'user'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - Admin 6R Laundry</title>
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
                    <a href="customers.php" class="flex items-center space-x-3 bg-indigo-600 px-4 py-3 rounded-lg">
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
                    <h1 class="text-2xl font-bold text-gray-800">Kelola Pelanggan</h1>
                    <p class="text-gray-600">Daftar semua pelanggan terdaftar</p>
                </div>
            </header>
            
            <main class="p-6">
                <!-- Stats -->
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Total Pelanggan</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $customers->num_rows; ?></h3>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-users text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Pelanggan Aktif</p>
                                <h3 class="text-3xl font-bold text-gray-800">
                                    <?php 
                                    $active = $conn->query("SELECT COUNT(DISTINCT user_id) as total FROM orders WHERE status != 'completed' AND status != 'cancelled'")->fetch_assoc()['total'];
                                    echo $active;
                                    ?>
                                </h3>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-user-check text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm">Pelanggan Baru (Bulan Ini)</p>
                                <h3 class="text-3xl font-bold text-gray-800">
                                    <?php 
                                    $new_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user' AND MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)")->fetch_assoc()['total'];
                                    echo $new_customers;
                                    ?>
                                </h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-user-plus text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Customers Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">Daftar Pelanggan</h2>
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Cari pelanggan..."
                                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Nama</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Email</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Telepon</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Total Pesanan</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Total Belanja</th>
                                    <th class="text-left py-4 px-6 text-gray-600 font-semibold">Terdaftar</th>
                                    <th class="text-center py-4 px-6 text-gray-600 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="customerTable">
                                <?php if($customers->num_rows > 0): ?>
                                    <?php while($customer = $customers->fetch_assoc()): ?>
                                        <tr class="border-b hover:bg-gray-50 customer-row">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                                        <i class="fas fa-user text-indigo-600"></i>
                                                    </div>
                                                    <span class="font-semibold customer-name"><?php echo htmlspecialchars($customer['nama']); ?></span>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 customer-email"><?php echo htmlspecialchars($customer['email']); ?></td>
                                            <td class="py-4 px-6"><?php echo htmlspecialchars($customer['telepon'] ?? '-'); ?></td>
                                            <td class="py-4 px-6">
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                                    <?php echo $customer['total_orders']; ?> pesanan
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="font-semibold text-green-600">
                                                    Rp <?php echo number_format($customer['total_spent'] ?? 0, 0, ',', '.'); ?>
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-600">
                                                <?php echo date('d M Y', strtotime($customer['created_at'])); ?>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex justify-center space-x-2">
                                                    <button onclick="viewCustomer(<?php echo $customer['id']; ?>)" 
                                                            class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition"
                                                            title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="orders.php?customer=<?php echo $customer['id']; ?>"
                                                       class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition"
                                                       title="Lihat Pesanan">
                                                        <i class="fas fa-shopping-bag"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-500">
                                            <i class="fas fa-users text-4xl mb-2"></i>
                                            <p>Belum ada pelanggan terdaftar</p>
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

    <!-- Customer Detail Modal -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Detail Pelanggan</h2>
                    <button onclick="closeModal('customerModal')" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div id="customerModalContent" class="p-6">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.customer-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.customer-name').textContent.toLowerCase();
                const email = row.querySelector('.customer-email').textContent.toLowerCase();
                
                if(name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        function viewCustomer(customerId) {
            openModal('customerModal');
            document.getElementById('customerModalContent').innerHTML = '<div class="text-center py-8"><div class="loader mx-auto"></div></div>';
            
            // Fetch customer details (implement API endpoint)
            fetch(`../api/customer.php?id=${customerId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const customer = data.customer;
                        document.getElementById('customerModalContent').innerHTML = `
                            <div class="space-y-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600 text-3xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800">${customer.nama}</h3>
                                        <p class="text-gray-600">${customer.email}</p>
                                    </div>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Telepon</p>
                                        <p class="font-semibold">${customer.telepon || '-'}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Terdaftar</p>
                                        <p class="font-semibold">${customer.created_at}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Pesanan</p>
                                        <p class="font-semibold">${customer.total_orders} pesanan</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Belanja</p>
                                        <p class="font-semibold text-green-600">${formatRupiah(customer.total_spent)}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-600 mb-2">Alamat</p>
                                    <p class="font-semibold">${customer.alamat || '-'}</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error(error);
                    showToast('Gagal memuat data pelanggan', 'error');
                });
        }
    </script>
</body>
</html>