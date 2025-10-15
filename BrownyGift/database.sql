-- Database: brownygift
CREATE DATABASE brownygift CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE brownygift;

-- Tabel Buket
CREATE TABLE buket (
    id VARCHAR(10) PRIMARY KEY,
    nama_buket VARCHAR(100) NOT NULL,
    jenis_bunga VARCHAR(100) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(10, 2) NOT NULL,
    tanggal_masuk TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_update TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    buket_id VARCHAR(10) NOT NULL,
    qty INT NOT NULL,
    total_harga DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'selesai', 'dikirim') NOT NULL DEFAULT 'pending',
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    alamat TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buket_id) REFERENCES buket(id) ON DELETE CASCADE
);

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin', 'ekspedisi') NOT NULL DEFAULT 'customer',
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data awal untuk users (password plaintext)
INSERT INTO users (username, password, nama, role) VALUES
('nia', '123456', 'Nia', 'customer'),
('robi', '123456', 'Robi', 'admin'),
('eko', '123456', 'Eko', 'ekspedisi');

-- Insert data awal untuk buket
INSERT INTO buket (id, nama_buket, jenis_bunga, stok, harga) VALUES
('BUKET001', 'Buket Mawar Merah', 'Mawar', 10, 50000.00),
('BUKET002', 'Buket Tulip Kuning', 'Tulip', 15, 60000.00),
('BUKET003', 'Buket Anggrek Putih', 'Anggrek', 8, 75000.00);

-- Insert data awal untuk orders
INSERT INTO orders (customer_id, buket_id, qty, total_harga, status, alamat) VALUES
(1, 'BUKET001', 2, 100000.00, 'pending', 'Jl. Merdeka No. 123, Jakarta Pusat'),
(1, 'BUKET002', 1, 60000.00, 'processing', 'Jl. Merdeka No. 123, Jakarta Pusat'),
(1, 'BUKET003', 1, 75000.00, 'selesai', 'Jl. Merdeka No. 123, Jakarta Pusat');