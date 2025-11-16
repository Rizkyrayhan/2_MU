<?php
require_once '../config/database.php';

// Handle Logout
if(isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    redirect('index.php');
}

// Handle Login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = cleanInput($_POST['email']);
    $password = $_POST['password'];
    $login_type = isset($_POST['login_type']) ? $_POST['login_type'] : 'user'; // Detect login type
    
    if(empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email dan password harus diisi!';
        if($login_type == 'admin') {
            redirect('admin/login.php');
        } else {
            redirect('user/login.php');
        }
    }
    
    // Query user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if(password_verify($password, $user['password'])) {
            
            // Check if trying to login as admin but not admin
            if($login_type == 'admin' && $user['role'] != 'admin') {
                $_SESSION['error'] = 'Anda tidak memiliki akses admin!';
                redirect('admin/login.php');
            }
            
            // Check if trying to login as user but is admin
            if($login_type == 'user' && $user['role'] == 'admin') {
                $_SESSION['error'] = 'Gunakan halaman login admin!';
                redirect('user/login.php');
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if($user['role'] == 'admin') {
                redirect('admin/index.php');
            } else {
                // Check if there's a redirect URL
                if(isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    redirect($redirect_url);
                } else {
                    // User redirect to home page
                    redirect('index.php');
                }
            }
        } else {
            $_SESSION['error'] = 'Password salah!';
            if($login_type == 'admin') {
                redirect('admin/login.php');
            } else {
                redirect('user/login.php');
            }
        }
    } else {
        $_SESSION['error'] = 'Email tidak terdaftar!';
        if($login_type == 'admin') {
            redirect('admin/login.php');
        } else {
            redirect('user/login.php');
        }
    }
}
?>