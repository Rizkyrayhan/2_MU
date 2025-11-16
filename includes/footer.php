<footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center space-x-2 mb-4">
                        <div class="bg-white rounded-full p-2">
                            <img src="../assets/images/logo2_6R.png" alt="6R Laundry" class="w-9 h-6">
                        </div>
                        <span class="text-xl font-bold">Laundry</span>
                </div>
                <p class="text-gray-400 mb-4">Solusi laundry terpercaya dengan pelayanan profesional dan teknologi modern.</p>
                <div class="flex space-x-4">
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
            </div>
            
            <div>
                <h3 class="text-lg font-bold mb-4">Layanan</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="<?php echo BASE_URL; ?>user/layanan.php" class="hover:text-white transition">Cuci Kering</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/layanan.php" class="hover:text-white transition">Cuci Setrika</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/layanan.php" class="hover:text-white transition">Premium Care</a></li>
                    <li><a href="<?php echo BASE_URL; ?>user/layanan.php" class="hover:text-white transition">Express Service</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-lg font-bold mb-4">Perusahaan</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="<?php echo BASE_URL; ?>user/tentang.php" class="hover:text-white transition">Tentang Kami</a></li>
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
                        <span>JJl. Ki Maja Blok BB No.17, Way Halim Permai, Kec. Way Halim, Kota Bandar Lampung, Lampung 35132</span>
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
            <p>&copy; <?php echo date('Y'); ?> 6R Laundry. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" 
        class="hidden fixed bottom-8 right-8 bg-indigo-600 text-white w-12 h-12 rounded-full shadow-lg hover:bg-indigo-700 transition transform hover:scale-110 z-50">
    <i class="fas fa-arrow-up"></i>
</button>