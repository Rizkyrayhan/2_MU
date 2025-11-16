<?php 
require_once '../config/database.php';

if(!isLoggedIn() || !isAdmin()) {
    redirect('admin/login.php');
}

//Get Setatus
$pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];

$success = '';
$error = '';

// Handle profile update
if(isset($_POST['update_profile'])) {
    $nama = cleanInput($_POST['nama']);
    $email = cleanInput($_POST['email']);
    $telepon = cleanInput($_POST['telepon']);
    
    $sql = "UPDATE users SET nama = ?, email = ?, telepon = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nama, $email, $telepon, $_SESSION['user_id']);
    
    if($stmt->execute()) {
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        $success = "Profile berhasil diupdate!";
    } else {
        $error = "Gagal mengupdate profile.";
    }
}

// Handle password change
if(isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password from database
    $user = $conn->query("SELECT password FROM users WHERE id = " . $_SESSION['user_id'])->fetch_assoc();
    
    if(!password_verify($current_password, $user['password'])) {
        $error = "Password lama tidak sesuai!";
    } elseif($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi tidak cocok!";
    } elseif(strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password = '$hashed' WHERE id = " . $_SESSION['user_id']);
        $success = "Password berhasil diubah!";
    }
}

// Get current user data
$user = $conn->query("SELECT * FROM users WHERE id = " . $_SESSION['user_id'])->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin 6R Laundry</title>
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
                    <a href="services.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
                        <i class="fas fa-cog"></i>
                        <span>Layanan</span>
                    </a>
                    <a href="settings.php" class="flex items-center space-x-3 bg-indigo-600 px-4 py-3 rounded-lg">
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
                    <h1 class="text-2xl font-bold text-gray-800">Pengaturan</h1>
                    <p class="text-gray-600">Kelola pengaturan akun dan sistem</p>
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
                
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Profile Settings -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-3 text-indigo-600"></i>
                            Profile Admin
                        </h2>
                        
                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Telepon</label>
                                <input type="tel" name="telepon" value="<?php echo htmlspecialchars($user['telepon'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <button type="submit" name="update_profile"
                                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-lock mr-3 text-indigo-600"></i>
                            Ubah Password
                        </h2>
                        
                        <form method="POST" class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Password Lama</label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                                <input type="password" name="new_password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <button type="submit" name="change_password"
                                    class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                                <i class="fas fa-key mr-2"></i> Ubah Password
                            </button>
                        </form>
                    </div>
                    
                    <!-- System Info -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-indigo-600"></i>
                            Informasi Sistem
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Versi Sistem</span>
                                <span class="font-semibold">1.0.0</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">PHP Version</span>
                                <span class="font-semibold"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Database</span>
                                <span class="font-semibold">MySQL</span>
                            </div>
                            <div class="flex justify-between border-b pb-2">
                                <span class="text-gray-600">Server Time</span>
                                <span class="font-semibold"><?php echo date('d M Y H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-bolt mr-3 text-indigo-600"></i>
                            Aksi Cepat
                        </h2>
                        
                        <div class="space-y-3">
                            <button onclick="clearCache()" 
                                    class="w-full bg-blue-500 text-white py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                                <i class="fas fa-sync-alt mr-2"></i> Clear Cache
                            </button>
                            
                            <button onclick="exportData()" 
                                    class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                                <i class="fas fa-download mr-2"></i> Export Data
                            </button>
                            
                            <button onclick="viewLogs()" 
                                    class="w-full bg-gray-600 text-white py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                                <i class="fas fa-file-alt mr-2"></i> View Logs
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function clearCache() {
            if(confirm('Hapus cache sistem?')) {
                showToast('Cache berhasil dihapus', 'success');
            }
        }
        
        function exportData() {
            showToast('Export data dimulai...', 'info');
            // Implement export functionality
        }
        
        function viewLogs() {
            window.open('logs.php', '_blank');
        }
    </script>
</body>
</html>