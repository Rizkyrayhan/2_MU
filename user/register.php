<?php 
require_once '../config/database.php';

if(isLoggedIn()) {
    redirect('user/tracking.php');
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = cleanInput($_POST['nama']);
    $email = cleanInput($_POST['email']);
    $telepon = cleanInput($_POST['telepon']);
    $alamat = cleanInput($_POST['alamat']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validation
    if(empty($nama) || empty($email) || empty($password)) {
        $errors[] = 'Nama, email, dan password wajib diisi!';
    }
    
    if($password !== $confirm_password) {
        $errors[] = 'Password dan konfirmasi password tidak cocok!';
    }
    
    if(strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter!';
    }
    
    // Check if email already exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if($check->num_rows > 0) {
        $errors[] = 'Email sudah terdaftar!';
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (nama, email, telepon, alamat, password, role) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nama, $email, $telepon, $alamat, $hashed_password);
        
        if($stmt->execute()) {
            $_SESSION['success'] = 'Registrasi berhasil! Silakan login.';
            redirect('user/login.php');
        } else {
            $errors[] = 'Gagal mendaftar. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - 6R Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-indigo-600">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <div class="text-center mb-8 fade-in">
                <a href="../index.php" class="inline-flex items-center justify-center space-x-2 mb-4">
                    <div class="bg-white rounded-full p-3">
                        <i class="fas fa-tshirt text-indigo-600 text-3xl"></i>
                    </div>
                </a>
                <h2 class="text-3xl font-bold text-white">Daftar Akun Baru</h2>
                <p class="text-indigo-100 mt-2">Bergabunglah dengan 6R Laundry</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-2xl p-8 fade-in-delayed">
                <?php if(isset($errors) && !empty($errors)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <?php foreach($errors as $error): ?>
                            <p><i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" data-validate>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user mr-2"></i> Nama Lengkap *
                        </label>
                        <input type="text" name="nama" required
                               value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="John Doe">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-envelope mr-2"></i> Email *
                        </label>
                        <input type="email" name="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="email@example.com">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-phone mr-2"></i> Nomor Telepon
                        </label>
                        <input type="tel" name="telepon"
                               value="<?php echo isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : ''; ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="+62 812 3456 7890">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i> Alamat
                        </label>
                        <textarea name="alamat" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                                  placeholder="Alamat lengkap"><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2"></i> Password *
                        </label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock mr-2"></i> Konfirmasi Password *
                        </label>
                        <input type="password" name="confirm_password" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent form-input"
                               placeholder="Ulangi password">
                    </div>
                    
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition transform hover:scale-105 ripple">
                        <i class="fas fa-user-plus mr-2"></i> Daftar
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Sudah punya akun?
                        <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                            Login
                        </a>
                    </p>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>