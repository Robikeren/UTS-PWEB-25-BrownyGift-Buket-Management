<?php
// customer/cart.php
include '../config.php';
include '../auth.php';
checkRole('customer');

$customer_id = $_SESSION['user_id']; // ← Define customer_id
$total_harga = 0; // ← Initialize total_harga

// Add to cart atau update quantity
if (isset($_POST['add_to_cart'])) {
    $buket_id = $_POST['buket_id'];
    $qty = intval($_POST['qty']);
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$buket_id] = $qty;
    
    echo "<script>alert('Ditambahkan ke keranjang!'); window.location='cart.php';</script>";
}

// Remove from cart
if (isset($_GET['remove'])) {
    $buket_id = $_GET['remove'];
    unset($_SESSION['cart'][$buket_id]);
    echo "<script>window.location='cart.php';</script>";
}

// Checkout
if (isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        echo "<script>alert('Keranjang kosong!'); window.location='cart.php';</script>";
        exit;
    }
    
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']); // ← Escape alamat
    if (empty($alamat)) {
        echo "<script>alert('Alamat pengiriman wajib diisi!'); window.location='cart.php';</script>";
        exit;
    }
    
    $total_harga = 0; // ← Reset total_harga
    
    foreach ($_SESSION['cart'] as $buket_id => $qty) {
        $buket_query = mysqli_query($conn, "SELECT * FROM buket WHERE id='$buket_id' AND stok >= $qty");
        $buket = mysqli_fetch_assoc($buket_query);
        
        if ($buket) {
            $subtotal = $buket['harga'] * $qty;
            $total_harga += $subtotal; // ← Accumulate total
            
            // Fix SQL syntax - gunakan parameterized values
            $sql = "INSERT INTO orders (customer_id, buket_id, qty, total_harga, alamat, status, created_at) 
                    VALUES ('$customer_id', '$buket_id', '$qty', '$subtotal', '$alamat', 'pending', NOW())";
            
            if (mysqli_query($conn, $sql)) {
                // Kurangi stok
                $new_stok = $buket['stok'] - $qty;
                mysqli_query($conn, "UPDATE buket SET stok=$new_stok WHERE id='$buket_id'");
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location='cart.php';</script>";
                exit;
            }
        }
    }
    
    // Clear cart setelah checkout berhasil
    unset($_SESSION['cart']);
    echo "<script>
        alert('✅ Pesanan berhasil dibuat! Total: Rp" . number_format($total_harga, 0, ',', '.') . "');
        window.location='orders.php';
    </script>";
    exit;
}

// Hitung cart items
$cart_items = [];
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $query = mysqli_query($conn, "SELECT * FROM buket WHERE id='$id'");
        $buket = mysqli_fetch_assoc($query);
        if ($buket) {
            $subtotal = $buket['harga'] * $qty;
            $total += $subtotal;
            $cart_items[] = [
                'buket' => $buket,
                'qty' => $qty,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang - BrownyGift</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .cart-empty { text-align: center; padding: 3rem; background: var(--gray-50); border-radius: var(--radius-md); }
        .cart-total { background: var(--primary-pink); color: white; padding: 1rem; border-radius: var(--radius-md); margin-top: 1rem; }
        .address-field { width: 100%; padding: 1rem; border: 2px solid var(--gray-200); border-radius: var(--radius-sm); resize: vertical; }
        .actions { 
            display: flex; 
            gap: 1.5rem; /* ← Jarak lebih besar */
            align-items: center; 
            flex-wrap: wrap; 
            margin-bottom: 2rem; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛒 Keranjang Belanja</h1>
        <div class="actions">
            <a href="index.php" class="btn">🛍️ Kembali Belanja</a>
            <a href="orders.php" class="btn" style="margin-left: 1rem;">📋 Status Pesanan</a>
            <a href="../logout.php" class="btn delete">Logout</a>
        </div>

        <!-- Rest of cart content sama seperti sebelumnya -->
        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <h3>Keranjang kosong</h3>
                <p><a href="index.php" class="btn">Mulai belanja sekarang</a></p>
            </div>
        <?php else: ?>
            <!-- Table dan checkout form sama -->
            <!-- ... -->
            <form method="POST" style="margin-top: 2rem;">
                <label for="alamat">📍 Alamat Pengiriman Lengkap:</label>
                <textarea name="alamat" id="alamat" rows="3" class="address-field" required></textarea>
                
                <div class="actions" style="margin-top: 1.5rem; gap: 1.5rem;">
                    <a href="index.php" class="btn">← Kembali Belanja</a>
                    <button type="submit" name="checkout" class="btn" style="background: #10b981;">
                        ✅ Checkout & Bayar (Rp<?= number_format($total, 0, ',', '.'); ?>)
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>