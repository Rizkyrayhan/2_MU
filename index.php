<?php 
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6R Laundry - Layanan Laundry Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="font-sans">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                        <img src="assets/images/logo2_6R.png" alt="6R Laundry" class="w-15 h-10">
                    <span class="text-2xl font-bold text-gray-800">Laundry</span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-indigo-600 transition">Beranda</a>
                    <a href="user/layanan.php" class="text-gray-700 hover:text-indigo-600 transition">Layanan</a>
                    <a href="user/tentang.php" class="text-gray-700 hover:text-indigo-600 transition">Tentang Kami</a>
                    <a href="user/kontak.php" class="text-gray-700 hover:text-indigo-600 transition">Kontak</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-4">
                    <?php if(isLoggedIn()): ?>
                        <a href="user/tracking.php" class="text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-box"></i> Tracking
                        </a>
                        <span class="text-gray-600">Hi, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        <a href="user/pesan.php" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Pesan Sekarang
                        </a>
                        <a href="api/login.php?action=logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-indigo-600 hover:text-indigo-700 transition">Login</a>
                        <a href="user/pesan.php" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Pesan Sekarang
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-4">
                <a href="index.php" class="block py-2 text-gray-700 hover:text-indigo-600">Beranda</a>
                <a href="user/layanan.php" class="block py-2 text-gray-700 hover:text-indigo-600">Layanan</a>
                <a href="user/tentang.php" class="block py-2 text-gray-700 hover:text-indigo-600">Tentang Kami</a>
                <a href="user/kontak.php" class="block py-2 text-gray-700 hover:text-indigo-600">Kontak</a>
                <?php if(isLoggedIn()): ?>
                    <a href="user/tracking.php" class="block py-2 text-gray-700 hover:text-indigo-600">Tracking</a>
                    <a href="api/login.php?action=logout" class="block py-2 text-red-600">Logout</a>
                <?php else: ?>
                    <a href="user/login.php" class="block py-2 text-indigo-600">Login</a>
                    <a href="user/pesan.php" class="block py-2 bg-indigo-600 text-white px-4 rounded-lg mt-2 text-center">Pesan Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600 text-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-8 md:mb-0 fade-in">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        Layanan Laundry Terpercaya & Berkualitas
                    </h1>
                    <p class="text-lg mb-6 text-indigo-100">
                        Solusi lengkap untuk kebutuhan laundry Anda dengan teknologi modern dan pelayanan terbaik
                    </p>
                    <a href="user/pesan.php" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition transform hover:scale-105">
                        Mulai Pesan
                    </a>
                </div>
                <div class="md:w-1/2 fade-in-delayed">
                    <img src="assets/images/washers.jpg" alt="Laundry" class="rounded-lg shadow-2xl" onerror="this.src='https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=600'">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-4 fade-in">Mengapa Pilih 6R Laundry?</h2>
            <p class="text-center text-gray-600 mb-12 fade-in">Komitmen kami untuk memberikan pelayanan terbaik</p>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center feature-card">
                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Cepat & Tepat Waktu</h3>
                    <p class="text-gray-600">Pengerjaan cepat dengan jaminan tepat waktu sesuai janji</p>
                </div>
                
                <div class="text-center feature-card">
                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Aman & Terpercaya</h3>
                    <p class="text-gray-600">Pakaian Anda aman dengan sistem tracking dan asuransi</p>
                </div>
                
                <div class="text-center feature-card">
                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-leaf text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Ramah Lingkungan</h3>
                    <p class="text-gray-600">Menggunakan deterjen eco-friendly dan hemat air</p>
                </div>
                
                <div class="text-center feature-card">
                    <div class="bg-indigo-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-indigo-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Antar Jemput Gratis</h3>
                    <p class="text-gray-600">Layanan antar jemput gratis untuk area tertentu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 fade-in">
                    <img src="assets/images/laundromat.jpg" alt="About" class="rounded-lg shadow-lg" onerror="this.src='https://images.unsplash.com/photo-1517677208171-0bc6725a3e60?w=600'">
                </div>
                <div class="md:w-1/2 fade-in-delayed">
                    <h2 class="text-3xl font-bold mb-4">Tentang 6R Laundry</h2>
                    <p class="text-gray-600 mb-4">
                        6R Laundry hadir sebagai solusi modern untuk kebutuhan laundry Anda. Dengan pengalaman lebih dari 5 tahun, kami telah melayani ribuan pelanggan dengan kepuasan yang tinggi.
                    </p>
                    <p class="text-gray-600 mb-6">
                        Kami menggunakan teknologi terdepan dan bahan berkualitas tinggi untuk memastikan pakaian Anda bersih, wangi, dan terawat dengan baik.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Peralatan modern & canggih</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Deterjen premium & aman</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            <span>Staff profesional & berpengalaman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-4">Kata Pelanggan Kami</h2>
            <p class="text-center text-gray-600 mb-12">Kepuasan pelanggan adalah prioritas utama kami</p>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Budi Santoso</h4>
                            <div class="text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">Pelayanan sangat memuaskan! Pakaian bersih dan wangi. Sistem tracking nya juga memudahkan saya memantau cucian.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Siti Nurhaliza</h4>
                            <div class="text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">Harga terjangkau dengan kualitas premium. Staff ramah dan responsif. Recommended!</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md testimonial-card">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold">Ahmad Rizki</h4>
                            <div class="text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600">Layanan antar jemput gratis sangat membantu. Pengerjaan cepat dan hasil memuaskan!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Siap Mencoba Layanan Kami?</h2>
            <p class="text-lg mb-8 text-indigo-100">Dapatkan pengalaman laundry terbaik dengan teknologi modern dan pelayanan profesional</p>
            <a href="user/kontak.php" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition transform hover:scale-105">
                <i class="fas fa-phone-alt mr-2"></i> Hubungi Kami
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="bg-white rounded-full p-2">
                            <img src="assets/images/logo2_6R.png" alt="6R Laundry" class="w-9 h-6">
                        </div>
                        <span class="text-xl font-bold">Laundry</span>
                    </div>
                    <p class="text-gray-400">Solusi laundry terpercaya dengan pelayanan profesional dan teknologi modern.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center hover:bg-indigo-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center hover:bg-indigo-700 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center hover:bg-indigo-700 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                    <!-- Admin Login Link -->
                    <div class="mt-6">
                        <a href="admin/login.php" class="text-gray-400 hover:text-white text-sm flex items-center">
                            <i class="fas fa-lock mr-2"></i> Admin Login
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Layanan</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="user/layanan.php" class="hover:text-white transition">Cuci Kering</a></li>
                        <li><a href="user/layanan.php" class="hover:text-white transition">Cuci Setrika</a></li>
                        <li><a href="user/layanan.php" class="hover:text-white transition">Premium Care</a></li>
                        <li><a href="user/layanan.php" class="hover:text-white transition">Express Service</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Perusahaan</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="user/tentang.php" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">Karir</a></li>
                        <li><a href="#" class="hover:text-white transition">Kemitraan</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>Jl. Ki Maja Blok BB No.17, Way Halim Permai, Kec. Way Halim, Kota Bandar Lampung, Lampung 35132</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3"></i>
                            <span>+62 21 1234 5678</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>info@6rlaundry.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 6R Laundry. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>