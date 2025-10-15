<?php
// customer/index.php
include '../config.php';
include '../auth.php';
checkRole('customer');

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$search_sql = $keyword ? "WHERE nama_buket LIKE '%$keyword%' OR jenis_bunga LIKE '%$keyword%'" : "WHERE stok > 0";
$query = mysqli_query($conn, "SELECT * FROM buket $search_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Toko - BrownyGift</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .products { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 1.5rem; 
            margin-top: 2rem; 
        }
        .product-card { 
            background: white; 
            padding: 1.5rem; 
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-sm); 
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover { 
            transform: translateY(-4px); 
            box-shadow: var(--shadow-md); 
        }
        .product-card img { 
            width: 100%; 
            height: 200px; 
            object-fit: cover; 
            border-radius: var(--radius-sm); 
            margin-bottom: 1rem; 
        }
        .price { 
            font-size: 1.3rem; 
            font-weight: 700; 
            color: var(--primary-pink); 
            margin: 0.75rem 0; 
        }
        .cart-form { 
            display: flex; 
            gap: 0.75rem; 
            align-items: center; 
            margin-top: 1rem; 
        }
        .cart-form input[type="number"] { 
            width: 70px; 
            padding: 0.5rem; 
            border: 2px solid var(--gray-200); 
            border-radius: var(--radius-sm); 
        }
        .actions { 
            display: flex; 
            gap: 1.5rem; /* ← Jarak lebih besar antar tombol */
            align-items: center; 
            flex-wrap: wrap; 
            margin-bottom: 2rem; 
        }
        .search-container {
            display: flex;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem;
            border-radius: var(--radius-md);
            border: 2px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }
        .search-input {
            border: none;
            padding: 0.75rem;
            font-size: 1rem;
            outline: none;
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌸 Toko Buket BrownyGift 🌸</h1>
        
        <div class="actions">
            <a href="cart.php" class="btn">🛒 Keranjang</a>
            <a href="orders.php" class="btn" style="margin-left: 1rem;">📋 Status Pesanan</a>
            <a href="../logout.php" class="btn delete">Logout (<?= $_SESSION['username'] ?>)</a>
        </div>

        <!-- Search Form -->
        <div class="search-container">
            <form method="GET" style="display: flex; width: 100%;">
                <input 
                    type="text" 
                    name="keyword" 
                    class="search-input"
                    placeholder="🔍 Cari buket berdasarkan nama atau jenis..."
                    value="<?= htmlspecialchars($keyword); ?>"
                >
                <button type="submit" class="btn">Cari</button>
                <?php if ($keyword): ?>
                    <a href="index.php" class="btn delete" style="padding: 0.75rem 1rem;">✕ Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (mysqli_num_rows($query) == 0): ?>
            <?php if ($keyword): ?>
                <div class="alert warning">
                    Tidak ada buket ditemukan untuk "<strong><?= htmlspecialchars($keyword); ?></strong>".
                    <a href="index.php">Lihat semua buket</a>
                </div>
            <?php else: ?>
                <div class="alert warning">Tidak ada produk tersedia saat ini.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="products">
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <div class="product-card">
                    <?php if (!empty($row['gambar'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" 
                             alt="<?= htmlspecialchars($row['nama_buket']); ?>">
                    <?php endif; ?>
                    
                    <h3><?= htmlspecialchars($row['nama_buket']); ?></h3>
                    <p style="color: var(--gray-600);"><?= htmlspecialchars($row['jenis_bunga']); ?></p>
                    <div class="price">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></div>
                    <p style="color: var(--gray-500);">Stok: <?= $row['stok']; ?></p>
                    
                    <form method="POST" action="cart.php" class="cart-form">
                        <input type="hidden" name="buket_id" value="<?= $row['id']; ?>">
                        <input type="number" name="qty" value="1" min="1" max="<?= $row['stok']; ?>" required>
                        <button type="submit" name="add_to_cart" class="btn">Tambah Keranjang</button>
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>