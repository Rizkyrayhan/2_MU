<?php 
require_once '../config/database.php';

// Check if user is logged in
if(!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'user/pesan.php';
    redirect('user/login.php');
}

// Fetch services
$services = $conn->query("SELECT * FROM services WHERE status = 'active'");

// Get selected service if any
$selected_service = isset($_GET['service']) ? intval($_GET['service']) : null;

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_id = cleanInput($_POST['service_id']);
    $berat_kg = floatval($_POST['berat_kg']);
    $alamat_pickup = cleanInput($_POST['alamat_pickup']);
    $tanggal_pickup = cleanInput($_POST['tanggal_pickup']);
    $waktu_pickup = cleanInput($_POST['waktu_pickup']);
    $catatan = cleanInput($_POST['catatan']);
    
    // Get service price
    $service_query = $conn->query("SELECT harga_per_kg FROM services WHERE id = $service_id");
    $service_data = $service_query->fetch_assoc();
    $total_harga = $berat_kg * $service_data['harga_per_kg'];
    
    // Generate order code
    $kode_order = generateOrderCode();
    
    // Insert order
    $sql = "INSERT INTO orders (user_id, service_id, kode_order, berat_kg, total_harga, alamat_pickup, tanggal_pickup, waktu_pickup, catatan, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisddssss", $user_id, $service_id, $kode_order, $berat_kg, $total_harga, $alamat_pickup, $tanggal_pickup, $waktu_pickup, $catatan);
    
    if($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Add tracking entry
        $track_sql = "INSERT INTO order_tracking (order_id, status, keterangan) VALUES (?, 'pending', 'Pesanan berhasil dibuat')";
        $track_stmt = $conn->prepare($track_sql);
        $track_stmt->bind_param("i", $order_id);
        $track_stmt->execute();
        
        $_SESSION['success'] = "Pesanan berhasil dibuat! Kode order Anda: $kode_order";
        redirect('user/tracking.php');
    } else {
        $error = "Gagal membuat pesanan. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Layanan - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="flex items-center space-x-2">
                    <div class="bg-indigo-600 rounded-full p-2">
                        <i class="fas fa-tshirt text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">6R Laundry</span>
                </a>
                <div>
                    <span class="text-gray-600 mr-4">Hi, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                    <a href="../api/login.php?action=logout" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Buat Pesanan Baru</h1>
                    <p class="text-gray-600 mb-6">Isi form di bawah ini untuk memesan layanan laundry</p>
                    
                    <?php if(isset($error)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" data-validate class="space-y-6">
                        <!-- Service Selection -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                Pilih Layanan <span class="text-red-500">*</span>
                            </label>
                            <select name="service_id" id="service_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                                <option value="">-- Pilih Layanan --</option>
                                <?php while($service = $services->fetch_assoc()): ?>
                                    <option value="<?php echo $service['id']; ?>" 
                                            data-price="<?php echo $service['harga_per_kg']; ?>"
                                            <?php echo ($selected_service == $service['id']) ? 'selected' : ''; ?>>
                                        <?php echo $service['nama_layanan']; ?> - 
                                        Rp <?php echo number_format($service['harga_per_kg'], 0, ',', '.'); ?>/kg
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Weight -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                Perkiraan Berat (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="berat_kg" id="berat_kg" step="0.5" min="1" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                   placeholder="Contoh: 5">
                            <p class="text-sm text-gray-500 mt-1">Minimal 1 kg</p>
                        </div>
                        
                        <!-- Price Display -->
                        <div id="priceDisplay" class="hidden bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Estimasi Harga:</span>
                                <span id="totalPrice" class="text-2xl font-bold text-indigo-600">Rp 0</span>
                            </div>
                        </div>
                        
                        <!-- Pickup Address -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                Alamat Penjemputan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="alamat_pickup" required rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                      placeholder="Masukkan alamat lengkap untuk penjemputan"></textarea>
                        </div>
                        
                        <!-- Pickup Date & Time -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Tanggal Penjemputan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tanggal_pickup" required
                                       min="<?php echo date('Y-m-d'); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    Waktu Penjemputan <span class="text-red-500">*</span>
                                </label>
                                <select name="waktu_pickup" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                                    <option value="">-- Pilih Waktu --</option>
                                    <option value="08:00">08:00 - 10:00</option>
                                    <option value="10:00">10:00 - 12:00</option>
                                    <option value="13:00">13:00 - 15:00</option>
                                    <option value="15:00">15:00 - 17:00</option>
                                    <option value="17:00">17:00 - 19:00</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea name="catatan" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                      placeholder="Tambahkan catatan khusus untuk pesanan Anda"></textarea>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button type="submit"
                                    class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition ripple">
                                <i class="fas fa-check mr-2"></i> Buat Pesanan
                            </button>
                            <a href="../index.php"
                               class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition text-center">
                                <i class="fas fa-times mr-2"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="../assets/js/main.js"></script>
    <script>
        // Calculate price dynamically
        const serviceSelect = document.getElementById('service_id');
        const beratInput = document.getElementById('berat_kg');
        const priceDisplay = document.getElementById('priceDisplay');
        const totalPrice = document.getElementById('totalPrice');
        
        function calculatePrice() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const pricePerKg = parseFloat(selectedOption.dataset.price) || 0;
            const berat = parseFloat(beratInput.value) || 0;
            
            if(pricePerKg > 0 && berat > 0) {
                const total = pricePerKg * berat;
                totalPrice.textContent = formatRupiah(total);
                priceDisplay.classList.remove('hidden');
            } else {
                priceDisplay.classList.add('hidden');
            }
        }
        
        serviceSelect.addEventListener('change', calculatePrice);
        beratInput.addEventListener('input', calculatePrice);
    </script>
</body>
</html>