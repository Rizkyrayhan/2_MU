<?php 
require_once '../config/database.php';

// Fetch services from database
$query = "SELECT * FROM services WHERE status = 'active' ORDER BY harga_per_kg ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Kami - 6R Laundry</title>
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
                    <div class="bg-indigo-600 rounded-full p-2">
                        <i class="fas fa-tshirt text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold text-gray-800">6R Laundry</span>
                </a>
                
                <div class="hidden md:flex space-x-8">
                    <a href="../index.php" class="text-gray-700 hover:text-indigo-600 transition">Beranda</a>
                    <a href="layanan.php" class="text-indigo-600 font-semibold">Layanan</a>
                    <a href="tentang.php" class="text-gray-700 hover:text-indigo-600 transition">Tentang Kami</a>
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
                <a href="layanan.php" class="block py-2 text-indigo-600 font-semibold">Layanan</a>
                <a href="tentang.php" class="block py-2 text-gray-700">Tentang Kami</a>
                <a href="kontak.php" class="block py-2 text-gray-700">Kontak</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-12 bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 fade-in">Layanan Kami</h1>
            <p class="text-xl text-indigo-100 fade-in-delayed">Berbagai pilihan layanan laundry untuk memenuhi kebutuhan Anda</p>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <?php if($result && $result->num_rows > 0): ?>
                    <?php while($service = $result->fetch_assoc()): ?>
                        <div class="bg-white rounded-lg shadow-lg p-8 service-card hover:shadow-2xl transition-all duration-300">
                            <div class="text-center mb-6">
                                <?php 
                                // Map icon names to proper Font Awesome icons with colors
                                // Using only icons that are 100% available in Font Awesome 6.4
                                $iconConfig = [
                                    'tshirt' => ['icon' => 'fa-tshirt', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                    'shirt' => ['icon' => 'fa-tshirt', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
                                    'sparkles' => ['icon' => 'fa-wand-magic-sparkles', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                    'star' => ['icon' => 'fa-star', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
                                    'gem' => ['icon' => 'fa-gem', 'bg' => 'bg-pink-100', 'text' => 'text-pink-600'],
                                    'bolt' => ['icon' => 'fa-bolt', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                                    'crown' => ['icon' => 'fa-crown', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
                                    'soap' => ['icon' => 'fa-soap', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-600']
                                ];
                                
                                $config = isset($iconConfig[$service['icon']]) 
                                    ? $iconConfig[$service['icon']] 
                                    : ['icon' => 'fa-tshirt', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'];
                                ?>
                                <div class="w-20 h-20 <?php echo $config['bg']; ?> rounded-full flex items-center justify-center mx-auto mb-4 transform hover:scale-110 transition-transform duration-300">
                                    <i class="fas <?php echo $config['icon']; ?> <?php echo $config['text']; ?> text-3xl"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($service['nama_layanan']); ?>
                                </h3>
                                <p class="text-gray-600 mb-4">
                                    <?php echo htmlspecialchars($service['deskripsi']); ?>
                                </p>
                            </div>
                            
                            <div class="text-center mb-6">
                                <div class="text-4xl font-bold text-indigo-600 mb-2">
                                    Rp <?php echo number_format($service['harga_per_kg'], 0, ',', '.'); ?>/kg
                                </div>
                            </div>
                            
                            <a href="pesan.php?service=<?php echo $service['id']; ?>" 
                               class="block w-full bg-indigo-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-indigo-700 transition ripple">
                                Pilih Layanan
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-12">
                        <p class="text-gray-500">Belum ada layanan tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Additional Services -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Layanan Tambahan</h2>
            
            <div class="grid md:grid-cols-2 gap-8">
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <i class="fas fa-bolt text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Express Service</h3>
                            <p class="text-gray-600 mb-3">Layanan cuci kilat selesai dalam 6 jam untuk kebutuhan mendesak Anda</p>
                            <p class="text-indigo-600 font-bold">+Rp 5.000/kg</p>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <i class="fas fa-spray-can text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Parfum Premium</h3>
                            <p class="text-gray-600 mb-3">Tambahkan pewangi premium untuk pakaian yang lebih wangi tahan lama</p>
                            <p class="text-indigo-600 font-bold">+Rp 3.000/kg</p>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Anti Bakteri</h3>
                            <p class="text-gray-600 mb-3">Perlindungan ekstra dengan treatment anti bakteri dan tungau</p>
                            <p class="text-indigo-600 font-bold">+Rp 4.000/kg</p>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                    <div class="flex items-start space-x-4">
                        <div class="bg-indigo-100 p-3 rounded-lg">
                            <i class="fas fa-truck text-indigo-600 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2">Free Antar Jemput</h3>
                            <p class="text-gray-600 mb-3">Gratis layanan antar jemput untuk area tertentu dengan minimal 5kg</p>
                            <p class="text-green-600 font-bold">GRATIS</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Cara Kerja Kami</h2>
            
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold mb-2">Pesan Online</h3>
                    <p class="text-gray-600">Pilih layanan dan jadwalkan pickup melalui website kami</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold mb-2">Pickup Cucian</h3>
                    <p class="text-gray-600">Tim kami akan menjemput cucian di lokasi Anda</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold mb-2">Proses Laundry</h3>
                    <p class="text-gray-600">Cucian diproses dengan mesin modern dan deterjen berkualitas</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-indigo-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="text-xl font-bold mb-2">Delivery</h3>
                    <p class="text-gray-600">Cucian bersih diantar kembali ke alamat Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Siap Menggunakan Layanan Kami?</h2>
            <p class="text-xl mb-8 text-indigo-100">Pesan sekarang dan rasakan kemudahan laundry modern</p>
            <a href="pesan.php" class="inline-block bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition transform hover:scale-105">
                Pesan Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/main.js"></script>
</body>
</html>