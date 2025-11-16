<?php 
require_once '../config/database.php';

// Check login status
if(!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'user/pesan.php';
    redirect('login.php');
}

// Fetch active services
$services = $conn->query("SELECT * FROM services WHERE status = 'active'");

// Selected service (optional)
$selected_service = isset($_GET['service']) ? intval($_GET['service']) : null;

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_SESSION['user_id'];
    $service_id = intval($_POST['service_id']);
    $berat_kg = floatval($_POST['berat_kg']);
    $alamat_pickup = cleanInput($_POST['alamat_pickup']);
    $tanggal_pickup = cleanInput($_POST['tanggal_pickup']);
    $waktu_pickup = cleanInput($_POST['waktu_pickup']);
    $catatan = cleanInput($_POST['catatan']);

    // Cek service valid
    $service_query = $conn->prepare("SELECT harga_per_kg FROM services WHERE id = ?");
    $service_query->bind_param("i", $service_id);
    $service_query->execute();
    $service_result = $service_query->get_result();

    if($service_result->num_rows == 0) {
        $error = "Layanan tidak ditemukan!";
    } else {

        $service_data = $service_result->fetch_assoc();
        $harga_kg = floatval($service_data['harga_per_kg']);
        $total_harga = $berat_kg * $harga_kg;

        // Generate order code
        $kode_order = generateOrderCode();

        // Insert order
        $sql = "INSERT INTO orders 
                (user_id, service_id, kode_order, berat_kg, total_harga, alamat_pickup, tanggal_pickup, waktu_pickup, catatan, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);

        if(!$stmt){
            $error = "ERROR PREPARE: " . $conn->error;
        } else {
            $stmt->bind_param(
                "iisddssss",
                $user_id,
                $service_id,
                $kode_order,
                $berat_kg,
                $total_harga,
                $alamat_pickup,
                $tanggal_pickup,
                $waktu_pickup,
                $catatan
            );

            if($stmt->execute()) {

                $order_id = $conn->insert_id;

                // Tracking log
                $track_sql = "INSERT INTO order_tracking (order_id, status, keterangan) 
                              VALUES (?, 'pending', 'Pesanan berhasil dibuat')";
                $track_stmt = $conn->prepare($track_sql);
                $track_stmt->bind_param("i", $order_id);
                $track_stmt->execute();

                $_SESSION['success'] = "Pesanan berhasil dibuat! Kode order: $kode_order";
                redirect('user/tracking.php');

            } else {
                $error = "Gagal membuat pesanan: " . $stmt->error;
            }
        }
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
</head>

<body class="bg-gray-50">

    <!-- NAVBAR -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="../index.php" class="flex items-center space-x-2">
                <img src="../assets/images/logo2_6R.png" alt="6R Laundry" class="w-12">
                <span class="text-2xl font-bold text-gray-800">Laundry</span>
            </a>
            <div>
                <span class="text-gray-600 mr-4">Hi, <?= htmlspecialchars($_SESSION['nama']); ?></span>
                <a href="../api/login.php?action=logout" class="text-red-600 hover:text-red-700">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <section class="py-10">
        <div class="container mx-auto px-4 max-w-3xl">

            <div class="bg-white shadow-lg rounded-lg p-8">

                <h1 class="text-3xl font-bold mb-2">Buat Pesanan Baru</h1>
                <p class="text-gray-600 mb-6">Isi form berikut untuk memesan layanan laundry.</p>

                <!-- ERROR -->
                <?php if(isset($error)): ?>
                    <div class="bg-red-100 text-red-700 p-4 rounded border-l-4 border-red-500 mb-4">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">

                    <!-- LAYANAN -->
                    <div>
                        <label class="font-semibold">Pilih Layanan *</label>
                        <select name="service_id" id="service_id" required
                            class="w-full mt-2 p-3 border rounded-lg">
                            <option value="">-- Pilih Layanan --</option>

                            <?php while($srv = $services->fetch_assoc()): ?>
                                <option value="<?= $srv['id']; ?>"
                                    data-price="<?= $srv['harga_per_kg']; ?>"
                                    <?= $selected_service == $srv['id'] ? 'selected' : ''; ?>>
                                    <?= $srv['nama_layanan']; ?> - Rp <?= number_format($srv['harga_per_kg'],0,',','.'); ?>/kg
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- BERAT -->
                    <div>
                        <label class="font-semibold">Perkiraan Berat (kg) *</label>
                        <input type="number" name="berat_kg" id="berat_kg" step="0.5" min="1" required
                            class="w-full mt-2 p-3 border rounded-lg" placeholder="Contoh: 5">
                        <p class="text-sm text-gray-500">Minimal 1 kg</p>
                    </div>

                    <!-- ESTIMASI -->
                    <div id="priceDisplay" class="hidden bg-indigo-50 p-4 border rounded-lg">
                        <div class="flex justify-between">
                            <span class="font-semibold">Estimasi Harga:</span>
                            <span id="totalPrice" class="text-2xl font-bold text-indigo-600">Rp 0</span>
                        </div>
                    </div>

                    <!-- ALAMAT -->
                    <div>
                        <label class="font-semibold">Alamat Penjemputan *</label>
                        <textarea name="alamat_pickup" rows="3" required
                            class="w-full mt-2 p-3 border rounded-lg"></textarea>
                    </div>

                    <!-- TANGGAL & WAKTU -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="font-semibold">Tanggal Pickup *</label>
                            <input type="date" name="tanggal_pickup" min="<?= date('Y-m-d'); ?>" required
                                class="w-full mt-2 p-3 border rounded-lg">
                        </div>

                        <div>
                            <label class="font-semibold">Waktu Pickup *</label>
                            <select name="waktu_pickup" required
                                class="w-full mt-2 p-3 border rounded-lg">
                                <option value="">-- Pilih Waktu --</option>
                                <option value="08:00">08:00 - 10:00</option>
                                <option value="10:00">10:00 - 12:00</option>
                                <option value="13:00">13:00 - 15:00</option>
                                <option value="15:00">15:00 - 17:00</option>
                                <option value="17:00">17:00 - 19:00</option>
                            </select>
                        </div>
                    </div>

                    <!-- CATATAN -->
                    <div>
                        <label class="font-semibold">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3"
                            class="w-full mt-2 p-3 border rounded-lg"></textarea>
                    </div>

                    <!-- BUTTON -->
                    <div class="flex gap-4">
                        <button class="flex-1 bg-indigo-600 text-white p-3 rounded-lg font-semibold hover:bg-indigo-700">
                            Buat Pesanan
                        </button>

                        <a href="../index.php" 
                           class="flex-1 p-3 text-center rounded-lg bg-gray-200 text-gray-700 font-semibold">
                           Batal
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </section>

    <script>
        const service = document.getElementById('service_id');
        const berat = document.getElementById('berat_kg');
        const priceBox = document.getElementById('priceDisplay');
        const totalPrice = document.getElementById('totalPrice');

        function calc() {
            let harga = parseFloat(service.options[service.selectedIndex]?.dataset?.price || 0);
            let kg = parseFloat(berat.value || 0);

            if(harga > 0 && kg > 0) {
                let total = harga * kg;
                totalPrice.textContent = new Intl.NumberFormat('id-ID').format(total);
                priceBox.classList.remove('hidden');
            } else {
                priceBox.classList.add('hidden');
            }
        }

        service.addEventListener('change', calc);
        berat.addEventListener('input', calc);
    </script>

</body>
</html>
