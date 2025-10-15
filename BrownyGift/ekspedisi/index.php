<?php
// ekspedisi/index.php
include '../config.php';
include '../auth.php';
checkRole('ekspedisi');

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'selesai';
$where_clause = "o.status IN ('selesai', 'dikirim')";

if ($status_filter) {
    $where_clause = "o.status = '$status_filter'";
}

$query = mysqli_query($conn, "
    SELECT o.*, u.nama as customer_nama, b.nama_buket, b.gambar
    FROM orders o 
    JOIN users u ON o.customer_id = u.id 
    JOIN buket b ON o.buket_id = b.id 
    WHERE $where_clause 
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekspedisi - BrownyGift</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-selesai { background: #fef3c7; color: #d97706; }
        .status-dikirim { background: #dcfce7; color: #166534; }
        .order-card {
            background: white;
            margin: 1rem 0;
            padding: 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-pink);
        }
        .order-actions { 
            display: flex; 
            gap: 0.75rem; 
            margin-top: 1rem; 
        }
        .filter-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        .filter-tab {
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        .filter-tab.active {
            background: var(--primary-pink);
            color: white;
            border-color: var(--primary-pink);
        }
        .filter-tab:hover:not(.active) {
            background: var(--gray-100);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚚 Dashboard Ekspedisi</h1>
        
        <div class="filter-tabs">
            <a href="index.php" class="filter-tab <?= $status_filter == 'selesai' ? 'active' : '' ?>">📦 Siap Dikirim</a>
            <a href="?status=dikirim" class="filter-tab <?= $status_filter == 'dikirim' ? 'active' : '' ?>">🚛 Sedang Dikirim</a>
        </div>

        <div class="actions">
            <a href="../logout.php" class="btn delete">Logout (<?= $_SESSION['username'] ?>)</a>
        </div>

        <?php if (mysqli_num_rows($query) == 0): ?>
            <div class="alert warning">
                <?php if ($status_filter == 'selesai'): ?>
                    Tidak ada pesanan yang siap dikirim. Tunggu konfirmasi admin.
                <?php else: ?>
                    Belum ada pesanan yang sedang dikirim.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php while($order = mysqli_fetch_assoc($query)): ?>
            <div class="order-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3>Order #<?= $order['id']; ?></h3>
                    <span class="status-badge status-<?= $order['status']; ?>">
                        <?= ucfirst(str_replace('_', ' ', $order['status'])); ?>
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <h4>📋 Detail Pesanan</h4>
                        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_nama']); ?></p>
                        <p><strong>Buket:</strong> <?= htmlspecialchars($order['nama_buket']); ?></p>
                        <p><strong>Qty:</strong> <?= $order['qty']; ?></p>
                        <p><strong>Total:</strong> Rp<?= number_format($order['total_harga'], 0, ',', '.'); ?></p>
                        <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    
                    <div>
                        <h4>📍 Pengiriman</h4>
                        <div style="background: var(--gray-100); padding: 1rem; border-radius: var(--radius-sm); max-height: 120px; overflow-y: auto;">
                            <p style="white-space: pre-line; margin: 0;"><?= htmlspecialchars($order['alamat']); ?></p>
                        </div>
                        
                        <?php if($order['gambar']): ?>
                        <div style="margin-top: 1rem; text-align: center;">
                            <img src="../uploads/<?= htmlspecialchars($order['gambar']); ?>" 
                                 alt="Buket" width="80" height="80" 
                                 style="object-fit: cover; border-radius: var(--radius-sm);">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="order-actions">
                    <?php if ($order['status'] == 'selesai'): ?>
                        <a href="update.php?id=<?= $order['id']; ?>&status=dikirim" 
                           class="btn" 
                           style="background: #059669;"
                           onclick="return confirm('Konfirmasi pesanan #<?= $order['id']; ?> sudah dikirim?')">
                           🚚 Tandai Dikirim
                        </a>
                    <?php elseif ($order['status'] == 'dikirim'): ?>
                        <span class="status-badge status-dikirim" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                            ✅ Sudah Dikirim
                        </span>
                    <?php endif; ?>
                    
                    <a href="view.php?id=<?= $order['id']; ?>" class="btn secondary">👁️ Detail</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>
</html>