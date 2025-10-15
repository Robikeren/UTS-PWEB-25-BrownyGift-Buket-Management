<?php
// ekspedisi/view.php
include '../config.php';
include '../auth.php';
checkRole('ekspedisi');

$id = intval($_GET['id']);
$query = mysqli_query($conn, "
    SELECT o.*, u.nama as customer_nama, b.nama_buket, b.gambar
    FROM orders o 
    JOIN users u ON o.customer_id = u.id 
    JOIN buket b ON o.buket_id = b.id 
    WHERE o.id = $id
");
$order = mysqli_fetch_assoc($query);

if (!$order) {
    echo "<script>alert('Order tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Order #<?= $id; ?> - Ekspedisi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-badge { padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600; }
        .status-selesai { background: #fef3c7; color: #d97706; }
        .status-dikirim { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Order #<?= $order['id']; ?></h1>
        <div class="actions">
            <a href="index.php" class="btn">← Kembali</a>
            <a href="../logout.php" class="btn delete">Logout</a>
        </div>
        
        <div style="background: white; padding: 2rem; border-radius: var(--radius-lg); margin-top: 1rem; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>Order #<?= $order['id']; ?></h2>
                <span class="status-badge status-<?= $order['status']; ?>">
                    <?= ucfirst(str_replace('_', ' ', $order['status'])); ?>
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div>
                    <h3>👤 Customer</h3>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($order['customer_nama']); ?></p>
                    
                    <h3>📦 Pesanan</h3>
                    <p><strong>Buket:</strong> <?= htmlspecialchars($order['nama_buket']); ?></p>
                    <p><strong>Quantity:</strong> <?= $order['qty']; ?></p>
                    <p><strong>Total:</strong> <strong>Rp<?= number_format($order['total_harga'], 0, ',', '.'); ?></strong></p>
                </div>
                
                <div>
                    <h3>📍 Alamat Pengiriman</h3>
                    <div style="background: var(--gray-100); padding: 1.5rem; border-radius: var(--radius-md);">
                        <p style="white-space: pre-line; line-height: 1.6; margin: 0;"><?= htmlspecialchars($order['alamat']); ?></p>
                    </div>
                    
                    <h3>📅 Timeline</h3>
                    <p><strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    <?php if(isset($order['updated_at'])): ?>
                    <p><strong>Diupdate:</strong> <?= date('d/m/Y H:i', strtotime($order['updated_at'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($order['status'] == 'selesai'): ?>
            <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                <a href="update.php?id=<?= $order['id']; ?>&status=dikirim" 
                   class="btn" 
                   style="background: #059669; padding: 1rem 2rem;"
                   onclick="return confirm('Konfirmasi pengiriman order #<?= $order['id']; ?>?')">
                   🚚 Tandai Sebagai Dikirim
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>