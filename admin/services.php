<?php 
require_once '../config/database.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('admin/login.php');
}

$success = '';
$error = '';

// Handle Add Service
if(isset($_POST['add_service'])) {
    $nama = cleanInput($_POST['nama_layanan']);
    $deskripsi = cleanInput($_POST['deskripsi']);
    $harga = floatval($_POST['harga_per_kg']);
    $icon = cleanInput($_POST['icon']);
    
    $sql = "INSERT INTO services (nama_layanan, deskripsi, harga_per_kg, icon) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $nama, $deskripsi, $harga, $icon);
    
    if($stmt->execute()) {
        $success = "Layanan berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan layanan.";
    }
}

//Get Setatus
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];

// Handle Update Service
if(isset($_POST['update_service'])) {
    $id = intval($_POST['service_id']);
    $nama = cleanInput($_POST['nama_layanan']);
    $deskripsi = cleanInput($_POST['deskripsi']);
    $harga = floatval($_POST['harga_per_kg']);
    $icon = cleanInput($_POST['icon']);
    $status = cleanInput($_POST['status']);
    
    $sql = "UPDATE services SET nama_layanan=?, deskripsi=?, harga_per_kg=?, icon=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $nama, $deskripsi, $harga, $icon, $status, $id);
    
    if($stmt->execute()) {
        $success = "Layanan berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate layanan.";
    }
}

// Handle Delete Service
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if($conn->query("DELETE FROM services WHERE id = $id")) {
        $success = "Layanan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus layanan.";
    }
}

// Get all services
$services = $conn->query("SELECT * FROM services ORDER BY harga_per_kg ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Layanan - Admin 6R Laundry</title>
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
                    <a href="customers.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-users"></i>
                        <span>Pelanggan</span>
                    </a>
                    <a href="services.php" class="flex items-center space-x-3 bg-indigo-600 px-4 py-3 rounded-lg">
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
                <div class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Kelola Layanan</h1>
                        <p class="text-gray-600">Atur layanan laundry yang tersedia</p>
                    </div>
                    <button onclick="openModal('addServiceModal')" 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-plus mr-2"></i> Tambah Layanan
                    </button>
                </div>
            </header>
            
            <main class="p-6">
                <?php if($success): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded alert-dismissible">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded alert-dismissible">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Services Grid -->
                <div class="grid md:grid-cols-3 gap-6">
                    <?php if($services->num_rows > 0): ?>
                        <?php while($service = $services->fetch_assoc()): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="bg-indigo-100 w-16 h-16 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-<?php echo $service['icon']; ?> text-indigo-600 text-2xl"></i>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $service['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst($service['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                                        <?php echo htmlspecialchars($service['nama_layanan']); ?>
                                    </h3>
                                    <p class="text-gray-600 text-sm mb-4">
                                        <?php echo htmlspecialchars($service['deskripsi']); ?>
                                    </p>
                                    
                                    <div class="mb-4">
                                        <span class="text-3xl font-bold text-indigo-600">
                                            Rp <?php echo number_format($service['harga_per_kg'], 0, ',', '.'); ?>
                                        </span>
                                        <span class="text-gray-600">/kg</span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button onclick='editService(<?php echo json_encode($service); ?>)'
                                                class="flex-1 bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="deleteService(<?php echo $service['id']; ?>, '<?php echo $service['nama_layanan']; ?>')"
                                                class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-3 text-center py-12">
                            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                            <p class="text-gray-500">Belum ada layanan tersedia</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal" class="modal">
        <div class="modal-content max-w-2xl">
            <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold">Tambah Layanan Baru</h2>
            </div>
            <div class="p-6">
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Layanan</label>
                        <input type="text" name="nama_layanan" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga per Kg</label>
                        <input type="number" name="harga_per_kg" step="100" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Icon (Font Awesome)</label>
                        <select name="icon" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="tshirt">Shirt (tshirt)</option>
                            <option value="sparkles">Sparkles (sparkles)</option>
                            <option value="gem">Gem (gem)</option>
                            <option value="star">Star (star)</option>
                            <option value="crown">Crown (crown)</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" name="add_service"
                                class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-plus mr-2"></i> Tambah
                        </button>
                        <button type="button" onclick="closeModal('addServiceModal')"
                                class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal" class="modal">
        <div class="modal-content max-w-2xl">
            <div class="bg-indigo-600 text-white p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold">Edit Layanan</h2>
            </div>
            <div class="p-6">
                <form method="POST" id="editServiceForm" class="space-y-4">
                    <input type="hidden" name="service_id" id="edit_service_id">
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Layanan</label>
                        <input type="text" name="nama_layanan" id="edit_nama" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="3" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga per Kg</label>
                        <input type="number" name="harga_per_kg" id="edit_harga" step="100" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Icon</label>
                        <select name="icon" id="edit_icon" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="tshirt">Shirt</option>
                            <option value="sparkles">Sparkles</option>
                            <option value="gem">Gem</option>
                            <option value="star">Star</option>
                            <option value="crown">Crown</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" id="edit_status" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" name="update_service"
                                class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                        <button type="button" onclick="closeModal('editServiceModal')"
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
        function editService(service) {
            document.getElementById('edit_service_id').value = service.id;
            document.getElementById('edit_nama').value = service.nama_layanan;
            document.getElementById('edit_deskripsi').value = service.deskripsi;
            document.getElementById('edit_harga').value = service.harga_per_kg;
            document.getElementById('edit_icon').value = service.icon;
            document.getElementById('edit_status').value = service.status;
            openModal('editServiceModal');
        }
        
        function deleteService(id, nama) {
            if(confirm(`Hapus layanan "${nama}"?`)) {
                window.location.href = `services.php?delete=${id}`;
            }
        }
    </script>
</body>
</html>