<?php
require_once '../config/database.php';

// This file is for future AJAX registration if needed
// Currently registration is handled in user/register.php directly

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');
    
    $nama = cleanInput($_POST['nama']);
    $email = cleanInput($_POST['email']);
    $telepon = cleanInput($_POST['telepon'] ?? '');
    $alamat = cleanInput($_POST['alamat'] ?? '');
    $password = $_POST['password'];
    
    // Validation
    if(empty($nama) || empty($email) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Nama, email, dan password wajib diisi!'
        ]);
        exit;
    }
    
    if(strlen($password) < 6) {
        echo json_encode([
            'success' => false,
            'message' => 'Password minimal 6 karakter!'
        ]);
        exit;
    }
    
    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if($check->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email sudah terdaftar!'
        ]);
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (nama, email, telepon, alamat, password, role) VALUES (?, ?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nama, $email, $telepon, $alamat, $hashed_password);
    
    if($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Registrasi berhasil!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mendaftar. Silakan coba lagi.'
        ]);
    }
} else {
    header('Location: ../user/register.php');
}
?>