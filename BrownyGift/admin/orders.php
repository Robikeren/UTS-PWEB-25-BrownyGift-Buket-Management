<?php
// admin/orders.php
include '../config.php';
include 'auth.php';
checkRole('admin');

// Inisialisasi variabel status_filter dengan default
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$where_clause = $status_filter ? "WHERE o.status = '$status_filter'" : "";

// Pastikan koneksi database ada sebelum query
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Query dengan penanganan error
$query = mysqli_query($conn, "
    SELECT o.*, u.nama AS customer_nama, b.nama_buket
    FROM orders o 
    LEFT JOIN users u ON o.customer_id = u.id 
    LEFT JOIN buket b ON o.buket_id = b.id 
    $where_clause
    ORDER BY o.created_at DESC
");

if (!$query) {
    die("Error dalam query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pesanan - Admin</title>
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
        .status-dikirim { background: #d1fae5; color: #065f46; }
        .filter-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .filter-tab { 
            padding: 0.5rem 1rem; 
            border: 1px solid var(--gray-200); 
            border-radius: var(--radius-sm); 
            text-decoration: none; 
            font-weight: 500;
            transition: background 0.2s;
        }
        .filter-tab.active { 
            background: var(--primary-pink); 
            color: white; 
            border-color: var(--primary-pink);
        }
        .filter-tab:hover:not(.active) {
            background: var(--gray-100);
        }
        .action-buttons a { margin-right: 0.5rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--gray-200); }
        th { background: var(--gray-100); font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Manage Pesanan</h1>
        <div class="actions" style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <a href="index.php" class="btn">← Dashboard</a>
            <div class="filter-tabs">
                <a href="orders.php" class="filter-tab <?php echo (!$status_filter || $status_filter == 'pending') ? 'active' : ''; ?>">Menunggu</a>
                <a href="orders.php?status=processing" class="filter-tab <?php echo ($status_filter == 'processing') ? 'active' : ''; ?>">Proses</a>
                <a href="orders.php?status=selesai" class="filter-tab <?php echo ($status_filter == 'selesai') ? 'active' : ''; ?>">Selesai</a>
                <a href="orders.php?status=dikirim" class="filter-tab <?php echo ($status_filter == 'dikirim') ? 'active' : ''; ?>">Dikirim</a>
            </div>
            <a href="../logout.php" class="btn delete">Logout</a>
        </div>

        <?php if (mysqli_num_rows($query) == 0): ?>
            <div class="alert warning">
                <?php echo $status_filter ? "Tidak ada pesanan dengan status '$status_filter'." : "Belum ada pesanan."; ?>
            </div>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID Order</th>
                    <th>Customer</th>
                    <th>Buket</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td>#<?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['customer_nama'] ?? 'Tidak Diketahui'); ?></td>
                    <td><?= htmlspecialchars($row['nama_buket'] ?? 'Tidak Diketahui') . " (x" . htmlspecialchars($row['qty'] ?? 0) . ")"; ?></td>
                    <td>Rp<?= number_format($row['total_harga'] ?? 0, 0, ',', '.'); ?></td>
                    <td>
                        <span class="status-badge status-<?= htmlspecialchars($row['status'] ?? 'pending'); ?>">
                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($row['status'] ?? 'pending'))); ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now')); ?></td>
                    <td class="action-buttons">
                        <?php if (isset($row['status']) && $row['status'] == 'pending'): ?>
                            <a href="update_order.php?id=<?= $row['id']; ?>&status=processing" 
                               class="btn" 
                               onclick="return confirm('Konfirmasi mulai proses order #<?= $row['id']; ?>?')">
                               🔄 Proses
                            </a>
                        <?php elseif (isset($row['status']) && $row['status'] == 'processing'): ?>
                            <a href="update_order.php?id=<?= $row['id']; ?>&status=selesai" 
                               class="btn" 
                               onclick="return confirm('Konfirmasi pesanan #<?= $row['id']; ?> selesai diproses?')">
                               ✅ Selesai
                            </a>
                        <?php elseif (isset($row['status']) && $row['status'] == 'selesai'): ?>
                            <span style="color: #d97706;">⏳ Menunggu Ekspedisi</span>
                        <?php elseif (isset($row['status']) && $row['status'] == 'dikirim'): ?>
                            <span class="status-badge status-dikirim">🚚 Dikirim</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">Status Tidak Valid</span>
                        <?php endif; ?>
                        <a href="order_detail.php?id=<?= $row['id']; ?>" class="btn secondary" style="padding: 0.5rem;">👁️ Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>