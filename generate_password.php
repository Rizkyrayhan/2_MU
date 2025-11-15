<?php
/**
 * PASSWORD HASH GENERATOR
 * Letakkan file ini di root folder: C:\xampppp\htdocs\6R-laundry\generate_password.php
 * Akses via: http://localhost/6R-laundry/generate_password.php
 */

// Passwords to hash
$passwords = [
    'admin123' => '',
    'user123' => ''
];

// Generate hashes
foreach($passwords as $password => $hash) {
    $passwords[$password] = password_hash($password, PASSWORD_DEFAULT);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4f46e5;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 10px;
        }
        .password-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .password-box strong {
            color: #1f2937;
            display: block;
            margin-bottom: 5px;
        }
        .hash {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            color: #4f46e5;
            background: white;
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #e5e7eb;
        }
        .sql-code {
            background: #1f2937;
            color: #10b981;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
        }
        .btn {
            background: #4f46e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #4338ca;
        }
        .info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Password Hash Generator - 6R Laundry</h1>
        
        <div class="info">
            <strong>ℹ️ Informasi:</strong><br>
            Tool ini menggenerate password hash untuk database MySQL/MariaDB menggunakan PHP password_hash().
        </div>

        <?php foreach($passwords as $password => $hash): ?>
        <div class="password-box">
            <strong>Password: <?php echo $password; ?></strong>
            <div class="hash"><?php echo $hash; ?></div>
        </div>
        <?php endforeach; ?>

        <h2>📋 SQL Query untuk Update Database:</h2>
        <div class="sql-code">
-- Update Admin Password<br>
UPDATE users SET password = '<?php echo $passwords['admin123']; ?>' WHERE email = 'admin@6rlaundry.com';<br><br>

-- Update User Password<br>
UPDATE users SET password = '<?php echo $passwords['user123']; ?>' WHERE email = 'user@example.com';<br><br>

-- Verify<br>
SELECT id, nama, email, role FROM users;
        </div>

        <div class="warning">
            <strong>⚠️ Peringatan Keamanan:</strong><br>
            1. Hapus file ini setelah selesai digunakan!<br>
            2. Jangan deploy file ini ke production server!<br>
            3. Ganti password default setelah instalasi!
        </div>

        <h2>✅ Langkah-langkah Install:</h2>
        <ol>
            <li>Copy file SQL database: <code>database_6r_laundry.sql</code></li>
            <li>Buka phpMyAdmin: <code>http://localhost/phpmyadmin</code></li>
            <li>Import file SQL tersebut</li>
            <li>Atau jalankan SQL query di atas untuk update password</li>
            <li>Test login dengan kredensial di bawah</li>
        </ol>

        <h2>🔑 Login Credentials:</h2>
        <div class="password-box">
            <strong>👨‍💼 ADMIN</strong>
            Email: admin@6rlaundry.com<br>
            Password: admin123<br>
            URL: <a href="admin/login.php">admin/login.php</a>
        </div>
        
        <div class="password-box">
            <strong>👤 USER</strong>
            Email: user@example.com<br>
            Password: user123<br>
            URL: <a href="user/login.php">user/login.php</a>
        </div>

        <button class="btn" onclick="window.location.href='index.php'">← Kembali ke Home</button>
    </div>
</body>
</html>