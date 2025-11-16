<?php
require_once '../config/database.php';

// Set JSON header for all actions except print
$action = isset($_GET['action']) ? $_GET['action'] : '';
if($action != 'print') {
    header('Content-Type: application/json');
}

// Get Order Detail
if($action == 'detail') {
    $kode = isset($_GET['kode']) ? cleanInput($_GET['kode']) : (isset($_GET['kode_order']) ? cleanInput($_GET['kode_order']) : null);
    
    if(!$kode) {
        echo json_encode(['success' => false, 'message' => 'Kode order tidak ditemukan']);
        exit;
    }
    
    // Get order
    $order_query = "
        SELECT o.*, s.nama_layanan, u.nama as customer_name, u.email, u.telepon 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        JOIN users u ON o.user_id = u.id 
        WHERE o.kode_order = ?
    ";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $order_result = $stmt->get_result();
    
    if($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        
        // Get tracking
        $tracking_query = "SELECT * FROM order_tracking WHERE order_id = ? ORDER BY created_at DESC";
        $track_stmt = $conn->prepare($tracking_query);
        $track_stmt->bind_param("i", $order['id']);
        $track_stmt->execute();
        $tracking_result = $track_stmt->get_result();
        
        $tracking = [];
        while($track = $tracking_result->fetch_assoc()) {
            $tracking[] = [
                'status' => ucfirst($track['status']),
                'keterangan' => $track['keterangan'],
                'created_at' => date('d M Y H:i', strtotime($track['created_at']))
            ];
        }
        
        echo json_encode([
            'success' => true,
            'order' => $order,
            'tracking' => $tracking
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak ditemukan'
        ]);
    }
}

// Cancel Order
elseif($action == 'cancel' && isset($_GET['kode'])) {
    if(!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $kode = cleanInput($_GET['kode']);
    $user_id = $_SESSION['user_id'];
    
    // Check if order belongs to user and is pending
    $check = $conn->query("SELECT id FROM orders WHERE kode_order = '$kode' AND user_id = $user_id AND status = 'pending'");
    
    if($check->num_rows > 0) {
        $order = $check->fetch_assoc();
        
        // Update order status
        $conn->query("UPDATE orders SET status = 'cancelled' WHERE id = " . $order['id']);
        
        // Add tracking
        $conn->query("INSERT INTO order_tracking (order_id, status, keterangan) VALUES (" . $order['id'] . ", 'cancelled', 'Pesanan dibatalkan oleh pelanggan')");
        
        echo json_encode([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak dapat dibatalkan'
        ]);
    }
}

// Print Receipt
elseif($action == 'print' && isset($_GET['kode'])) {
    $kode = cleanInput($_GET['kode']);
    
    // Get order
    $order_query = "
        SELECT o.*, s.nama_layanan, u.nama as customer_name, u.email, u.telepon, u.alamat 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        JOIN users u ON o.user_id = u.id 
        WHERE o.kode_order = ?
    ";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("s", $kode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Struk - <?php echo $order['kode_order']; ?></title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 400px; margin: 20px auto; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #000; padding-bottom: 10px; }
                .info { margin: 10px 0; }
                .info-label { font-weight: bold; }
                .items { border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 10px 0; margin: 20px 0; }
                .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 20px; }
                @media print { .no-print { display: none; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>6R LAUNDRY</h2>
                <p>Jl. Sudirman No. 123, Lampung<br>
                Telp: +62 21 1234 5678</p>
            </div>
            
            <div class="info">
                <div><span class="info-label">Kode Order:</span> <?php echo $order['kode_order']; ?></div>
                <div><span class="info-label">Tanggal:</span> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></div>
                <div><span class="info-label">Customer:</span> <?php echo $order['customer_name']; ?></div>
                <div><span class="info-label">Telepon:</span> <?php echo $order['telepon']; ?></div>
            </div>
            
            <div class="items">
                <div><span class="info-label">Layanan:</span> <?php echo $order['nama_layanan']; ?></div>
                <div><span class="info-label">Berat:</span> <?php echo $order['berat_kg']; ?> kg</div>
                <div><span class="info-label">Harga/kg:</span> Rp <?php echo number_format($order['total_harga'] / $order['berat_kg'], 0, ',', '.'); ?></div>
            </div>
            
            <div class="total">
                Total: Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
            </div>
            
            <div style="text-align: center; margin-top: 30px; font-size: 12px;">
                <p>Terima kasih atas kepercayaan Anda!</p>
                <p>www.6rlaundry.com</p>
            </div>
            
            <div class="no-print" style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #6366f1; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Print
                </button>
            </div>
        </body>
        </html>
        <?php
    }
}

// Update Order Status (Admin Only)
elseif($action == 'update_status' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!isLoggedIn() || !isAdmin()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = intval($data['order_id']);
    $new_status = cleanInput($data['status']);
    $keterangan = cleanInput($data['keterangan']);
    
    // Update order
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $order_id);
    
    if($stmt->execute()) {
        // Add tracking
        $track_sql = "INSERT INTO order_tracking (order_id, status, keterangan) VALUES (?, ?, ?)";
        $track_stmt = $conn->prepare($track_sql);
        $track_stmt->bind_param("iss", $order_id, $new_status, $keterangan);
        $track_stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Status berhasil diupdate'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengupdate status'
        ]);
    }
}

else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>