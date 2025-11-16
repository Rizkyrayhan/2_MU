<?php 
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - 6R Laundry</title>
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
                    <a href="tentang.php" class="text-indigo-600 font-semibold">Tentang Kami</a>
                    <a href="kontak.php" class="text-gray-700 hover:text-indigo-600 transition">Kontak</a>
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
                <a href="tentang.php" class="block py-2 text-indigo-600 font-semibold">Tentang Kami</a>
                <a href="kontak.php" class="block py-2 text-gray-700">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-12 bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">Tentang 6R Laundry</h1>
            <p class="text-xl text-indigo-100 fade-in-delayed">Solusi laundry modern untuk kehidupan yang lebih mudah</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 fade-in">
                    <img src="https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?w=600" 
                         alt="Laundromat" class="rounded-lg shadow-lg">
                </div>
                <div class="md:w-1/2 fade-in-delayed">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Cerita Kami</h2>
                    <p class="text-gray-600 mb-4">
                        6R Laundry merupakan usaha jasa pelayanan laundry yang berlokasi di Jl. Ki Maja Blok BB No.17, Way Halim Permai, Kota Bandar Lampung. Usaha ini termasuk dalam kategori UMKM yang dikelola langsung oleh pemilik bersama beberapa karyawan, dan saat ini telah memiliki dua cabang yang aktif melayani pelanggan di wilayah Bandar Lampung.
                    </p>
                    <p class="text-gray-600 mb-4">
                        Dalam operasionalnya, 6R Laundry berkomitmen memberikan pelayanan yang cepat, efisien, dan ramah kepada pelanggan. Namun, proses pencatatan sebelumnya masih dilakukan secara manual melalui buku catatan, sehingga sering terjadi masalah seperti kehilangan data pelanggan, kesalahan pencatatan transaksi, serta sulitnya memantau status cucian secara akurat.
                    <p class="text-gray-600 mb-6">
                        6R Laundry berkomitmen untuk terus meningkatkan kualitas layanan demi kepuasan pelanggan, sekaligus memperkuat daya saing sebagai usaha laundry yang profesional di wilayah Bandar Lampung.
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-eye text-indigo-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Visi Kami</h3>
                    </div>
                    <p class="text-gray-600">
                        Menjadi penyedia layanan laundry terdepan di Indonesia yang dikenal dengan kualitas, kecepatan, dan inovasi teknologi, sambil tetap menjaga kelestarian lingkungan.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-8">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-4 rounded-lg mr-4">
                            <i class="fas fa-bullseye text-indigo-600 text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Misi Kami</h3>
                    </div>
                    <ul class="text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Memberikan layanan laundry berkualitas tinggi</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Menggunakan teknologi ramah lingkungan</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Memberikan harga yang kompetitif</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Membangun kepercayaan pelanggan</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Nilai-Nilai Kami</h2>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-star text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kualitas</h3>
                    <p class="text-gray-600">Kami tidak pernah berkompromi dengan kualitas layanan</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Kepercayaan</h3>
                    <p class="text-gray-600">Membangun hubungan jangka panjang dengan pelanggan</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-leaf text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Lingkungan</h3>
                    <p class="text-gray-600">Peduli pada kelestarian lingkungan hidup</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-red-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Inovasi</h3>
                    <p class="text-gray-600">Terus berinovasi untuk layanan terbaik</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Stats -->
<section class="py-16 bg-indigo-600 text-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
            <div>
                <div class="text-5xl font-bold mb-2">2+</div>
                <p class="text-indigo-200">Cabang</p>
            </div>
            <div>
                <div class="text-5xl font-bold mb-2">60%</div>
                <p class="text-indigo-200">Tingkat Kepuasan</p>
            </div>
            <div>
                <div class="text-5xl font-bold mb-2">3+</div>
                <p class="text-indigo-200">Tahun Pengalaman</p>
            </div>
        </div>
    </div>
</section>

    <!-- CTA -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Siap Bergabung Bersama Kami?</h2>
            <p class="text-xl text-gray-600 mb-8">Rasakan pengalaman laundry modern yang berbeda</p>
            <a href="pesan.php" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105">
                Pesan Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/main.js"></script>
</body>
</html>