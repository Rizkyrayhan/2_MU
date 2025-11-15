<?php 
require_once '../config/database.php';

// Redirect if already logged in
if(isLoggedIn()) {
    redirect('user/tracking.php');
}

$error = '';
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo -->
            <div class="text-center mb-8 fade-in">
                <a href="../index.php" class="inline-flex items-center justify-center space-x-2 mb-4">
                    <div class="bg-white rounded-full p-3">
                        <i class="fas fa-tshirt text-indigo-600 text-3xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-white">Selamat Datang!</h2>
                <p class="text-indigo-100 mt-2">Login untuk melanjutkan</p>
            </div>
            
            <!-- Login Card -->
            <div class="bg-white rounded-lg shadow-2xl p-8 fade-in-delayed">
                <?php if($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="../api/login.php" method="POST" data-validate>
                    <input type="hidden" name="login_type" value="user">
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-envelope mr-2"></i> Email
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="email@example.com">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2"></i> Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                   placeholder="********">
                            <button type="button" onclick="togglePassword()" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">
                            Lupa password?
                        </a>
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 ripple">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Belum punya akun?
                        <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>
                
                <div class="mt-6 text-center">
                    <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
            
            <!-- Demo Credentials -->
            <div class="mt-6 bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 text-white text-sm">
                <p class="font-semibold mb-2"><i class="fas fa-info-circle mr-2"></i> Demo Account:</p>
                <p>Email: user@example.com</p>
                <p>Password: user123</p>
                <p class="mt-2">Admin: admin@6rlaundry.com / admin123</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>