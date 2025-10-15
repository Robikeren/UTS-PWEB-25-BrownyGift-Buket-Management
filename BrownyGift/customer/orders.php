<?php
// customer/orders.php
include '../config.php';
include '../auth.php';
checkRole('customer');

$customer_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "
    SELECT o.*, b.nama_buket 
    FROM orders o 
    JOIN buket b ON o.buket_id = b.id 
    WHERE o.customer_id = $customer_id 
    ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan - BrownyGift</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-selesai { background: #dcfce7; color: #166534; }
        .order-row { 
            background: white; 
            margin-bottom: 1rem; 
            padding: 1rem; /* ← Padding dikurangi */
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-sm);
            border-left: 4px solid var(--primary-pink);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem; /* ← Margin dikurangi */
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem; /* ← Gap dikurangi */
            font-size: 0.9rem;
        }
        .order-details p {
            margin: 0.25rem 0; /* ← Margin dikurangi */
        }
        .actions { 
            display: flex; 
            gap: 1.5rem; /* ← Jarak tombol lebih besar */
            align-items: center; 
            flex-wrap: wrap; 
            margin-bottom: 2rem; 
        }
        .address-box {
            background: var(--gray-100); 
            padding: 0.75rem; /* ← Padding dikurangi */
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Status Pesanan Saya</h1>
        <div class="actions">
            <a href="index.php" class="btn">🛍️ Toko</a>
            <a href="cart.php" class="btn" style="margin-left: 1rem;">🛒 Keranjang</a>
            <a href="../logout.php" class="btn delete">Logout</a>
        </div>

        <?php if (mysqli_num_rows($query) == 0): ?>
            <div class="alert warning">
                Anda belum memiliki pesanan. 
                <a href="index.php">Mulai belanja sekarang</a>
            </div>
        <?php else: ?>
            <div style="margin-top: 1rem;">
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <div class="order-row">
                    <div class="order-header">
                        <h3 style="margin: 0; font-size: 1.1rem;">Order #<?= $row['id']; ?></h3>
                        <span class="status-badge status-<?= $row['status']; ?>">
                            <?= ucfirst(str_replace('_', ' ', $row['status'])); ?>
                        </span>
                    </div>
                    
                    <div class="order-details">
                        <div>
                            <p><strong>📦 Buket:</strong> <?= htmlspecialchars($row['nama_buket']); ?> (x<?= $row['qty']; ?>)</p>
                            <p><strong>💰 Total:</strong> Rp<?= number_format($row['total_harga'], 0, ',', '.'); ?></p>
                            <p><strong>📅 Dibuat:</strong> <?= date('d/m/Y', strtotime($row['created_at'])); ?></p>
                        </div>
                        
                        <div>
                            <p><strong>📍 Alamat:</strong></p>
                            <div class="address-box">
                                <?= htmlspecialchars($row['alamat']); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>