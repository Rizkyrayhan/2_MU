<?php 
require_once '../config/database.php';

$success = '';
$error = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = cleanInput($_POST['nama']);
    $email = cleanInput($_POST['email']);
    $telepon = cleanInput($_POST['telepon']);
    $pesan = cleanInput($_POST['pesan']);
    
    if(empty($nama) || empty($email) || empty($pesan)) {
        $error = "Nama, email, dan pesan wajib diisi!";
    } else {
        $sql = "INSERT INTO contacts (nama, email, telepon, pesan) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nama, $email, $telepon, $pesan);
        
        if($stmt->execute()) {
            $success = "Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda.";
            // Reset form
            $_POST = array();
        } else {
            $error = "Gagal mengirim pesan. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="../index.php" class="flex items-center space-x-2">
                        <img src="../assets/images/logo2_6R.png" alt="6R Laundry" class="w-15 h-10">
                    <span class="text-2xl font-bold text-gray-800">Laundry</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-indigo-600 transition">Beranda</a>
                    <a href="layanan.php" class="text-gray-700 hover:text-indigo-600 transition">Layanan</a>
                    <a href="tentang.php" class="text-gray-700 hover:text-indigo-600 transition">Tentang Kami</a>
                    <a href="kontak.php" class="text-indigo-600 font-semibold">Kontak</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(isLoggedIn()): ?>
                        <a href="tracking.php" class="text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-box"></i> Tracking
                        </a>
                        <span class="text-gray-600">Hi, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        <a href="../api/login.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="text-indigo-600 hover:text-indigo-700 transition">Login</a>
                        <a href="pesan.php" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Pesan Sekarang
                        </a>
                    <?php endif; ?>
                </div>
                
                <button id="mobileMenuBtn" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4">
                <a href="../index.php" class="block py-2 text-gray-700">Beranda</a>
                <a href="layanan.php" class="block py-2 text-gray-700">Layanan</a>
                <a href="tentang.php" class="block py-2 text-gray-700">Tentang Kami</a>
                <a href="kontak.php" class="block py-2 text-indigo-600 font-semibold">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-12 bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">Hubungi Kami</h1>
            <p class="text-xl text-indigo-100 fade-in-delayed">Kami siap membantu Anda</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Kirim Pesan</h2>
                    <p class="text-gray-600 mb-6">Silakan isi form di bawah ini, kami akan segera merespons</p>
                    
                    <?php if($success): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($error): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" data-validate class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user mr-2"></i> Nama Lengkap *
                            </label>
                            <input type="text" name="nama" required
                                   value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope mr-2"></i> Email *
                            </label>
                            <input type="email" name="email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-phone mr-2"></i> Nomor Telepon
                            </label>
                            <input type="tel" name="telepon"
                                   value="<?php echo isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : ''; ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-comment-dots mr-2"></i> Pesan *
                            </label>
                            <textarea name="pesan" rows="5" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                      placeholder="Tulis pesan Anda di sini..."><?php echo isset($_POST['pesan']) ? htmlspecialchars($_POST['pesan']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 ripple">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Informasi Kontak</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-map-marker-alt text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Alamat</h3>
                                    <p class="text-gray-600">Jl. Ki Maja Blok BB No.17, Way Halim Permai, <br>Kec. Way Halim, Kota Bandar Lampung, Lampung 35132</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-phone text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Telepon</h3>
                                    <p class="text-gray-600">+62 821 8022 2255 (WhatsApp)</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-envelope text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                                    <p class="text-gray-600">info@6rlaundry.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-clock text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1">Jam Operasional</h3>
                                    <p class="text-gray-600">Senin - Jumat: 08.00 - 20.00</p>
                                    <p class="text-gray-600">Sabtu - Minggu: 09.00 - 18.00</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Ikuti Kami</h2>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/share/1GmoNZdZKb/" class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                            <a href="https://www.instagram.com/6r.laundry?igsh=MWlncjg3b2ExZWJ4eA==" class="w-12 h-12 bg-pink-600 text-white rounded-full flex items-center justify-center hover:bg-pink-700 transition">
                                <i class="fab fa-instagram text-xl"></i>
                            </a>
                            <a href="https://wa.me/6282180222255" class="w-12 h-12 bg-green-600 text-white rounded-full flex items-center justify-center hover:bg-green-700 transition">
                                <i class="fab fa-whatsapp text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Lokasi Kami</h2>
            <div class="rounded-lg overflow-hidden shadow-lg" style="height: 400px;">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.2542313682!2d105.26975757600074!3d-5.378155253791169!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40dbb490c37013%3A0x7910e01105d4eade!2s6R%20LAUNDRY%202!5e0!3m2!1sid!2sid!4v1763257657162!5m2!1sid!2sid"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Pertanyaan yang Sering Diajukan</h2>
            
            <div class="max-w-3xl mx-auto space-y-4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Berapa lama waktu yang dibutuhkan untuk mencuci?</h3>
                    <p class="text-gray-600">Untuk layanan reguler, kami membutuhkan waktu 2-3 hari. Untuk layanan express, kami dapat menyelesaikan dalam 6-12 jam.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Apakah ada minimum order?</h3>
                    <p class="text-gray-600">Minimum order kami adalah 2kg untuk layanan reguler. Untuk layanan express, minimum 3kg.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Bagaimana cara pembayaran?</h3>
                    <p class="text-gray-600">Kami hanya menerima pembayaran tunai saat kurir mengambil pesanan.</p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-bold text-gray-800 mb-2">Apakah ada garansi jika pakaian rusak?</h3>
                    <p class="text-gray-600">Ya, kami memberikan garansi 100% untuk kerusakan yang disebabkan oleh kesalahan kami dalam proses pencucian.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/main.js"></script>
</body>
</html>