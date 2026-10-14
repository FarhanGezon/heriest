-- =======================================================
-- Database: heriest_db
-- Aplikasi: HERIEST CV. ADVERTISING (Katalog & Admin Panel)
-- =======================================================

CREATE DATABASE IF NOT EXISTS `heriest_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `heriest_db`;

-- --------------------------------------------------------
-- 1. Table structure for `admins` (Tabel Administrator)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default login: admin / admin123
INSERT INTO `admins` (`id`, `username`, `password`, `nama_lengkap`, `email`) VALUES
(1, 'admin', '$2y$10$Yuuw0UlDngh5iaVy1.XQ9eK4cvORXd5RWSsDVuChiU2/LUfYiaEZy', 'Administrator Heriest', 'admin@heriest.com')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- --------------------------------------------------------
-- 2. Table structure for `kategori` (Kategori Produk)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`id`, `nama_kategori`, `slug`) VALUES
(1, 'Neon Box', 'neon-box'),
(2, 'Cetak Banner', 'cetak-banner'),
(3, 'Huruf Timbul', 'huruf-timbul'),
(4, 'Merchandise', 'merchandise'),
(5, 'Sablon Kaos', 'sablon-kaos')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- --------------------------------------------------------
-- 3. Table structure for `produk` (Katalog Produk)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `produk` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kategori_id` INT NULL,
  `nama_produk` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT NULL,
  `harga` INT NOT NULL DEFAULT 50000,
  `gambar` VARCHAR(255) NULL,
  `wa_text` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `produk` (`id`, `kategori_id`, `nama_produk`, `deskripsi`, `harga`, `gambar`, `wa_text`, `is_active`) VALUES
(1, 1, 'Neon Box Custom', 'Bahan akrilik premium & LED terang hemat energi', 50000, NULL, 'Halo admin, saya ingin pesan Neon Box Custom', 1),
(2, 1, 'Neon Box Akrilik', 'Bentuk presisi dengan bingkai aluminium kokoh', 50000, NULL, 'Halo admin, saya ingin pesan Neon Box Akrilik', 1),
(3, 1, 'Neon Box Bulat', 'Desain 2 sisi cocok untuk cafe & plang toko', 50000, NULL, 'Halo admin, saya ingin pesan Neon Box Bulat', 1),
(4, 1, 'Neon Box LED', 'Pencahayaan LED merata dan tahan hujan outdoor', 50000, NULL, 'Halo admin, saya ingin pesan Neon Box LED', 1),
(5, 5, 'Kaos Sablon DTF', 'Cotton combed 30s premium lembut warna tajam', 50000, NULL, 'Halo admin, saya ingin pesan Kaos Sablon DTF', 1),
(6, 5, 'Kaos Custom Komunitas', 'Sablon plastisol / DTF kualitas distro tahan lama', 50000, NULL, 'Halo admin, saya ingin pesan Kaos Custom Komunitas', 1),
(7, 2, 'Spanduk Banner Flexi', 'Bahan flexi korea 440gsm tajam anti pudar', 50000, NULL, 'Halo admin, saya ingin pesan Spanduk Banner', 1),
(8, 2, 'Roll Up Banner 60x160', 'Kaki aluminium kokoh praktis untuk pameran / event', 50000, NULL, 'Halo admin, saya ingin pesan Roll Up Banner', 1),
(9, 3, 'Huruf Timbul Stainless', 'Tampilan mewah elegan tahan karat kilap mirror', 50000, NULL, 'Halo admin, saya ingin pesan Huruf Timbul Stainless', 1),
(10, 3, 'Huruf Timbul Akrilik LED', 'Backlight LED menyala indah di malam hari', 50000, NULL, 'Halo admin, saya ingin pesan Huruf Timbul Akrilik LED', 1),
(11, 4, 'Souvenir & Merchandise Mug', 'Cetak sublime awet lengkap dus kemasan cantik', 50000, NULL, 'Halo admin, saya ingin pesan Merchandise Mug', 1),
(12, 2, 'X-Banner Promosi Stand', 'Bahan albatros doff / glossy lengkap tiang fiber', 50000, NULL, 'Halo admin, saya ingin pesan X-Banner Promosi Stand', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- --------------------------------------------------------
-- 4. Table structure for `pengaturan` (Informasi Website)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `label` VARCHAR(100) NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pengaturan` (`setting_key`, `setting_value`, `label`) VALUES
('nama_toko', 'HERIEST CV. ADVERTISING', 'Nama Perusahaan/Toko'),
('tagline', 'We Touch With Colours.', 'Tagline Bisnis'),
('whatsapp', '082264356885', 'Nomor WhatsApp Utama'),
('email', 'info@heriest.com', 'Alamat Email'),
('alamat', 'Jl. kabupaten nomor mangunsari, rego, kabupaten, manandai, kabupaten, kabupaten karanganyar, Jawa Timur 63281', 'Alamat Workshop'),
('tiktok', '#', 'Link Akun TikTok'),
('instagram', '#', 'Link Akun Instagram'),
('popup_enabled', '0', 'Tampilkan popup promo saat dibuka'),
('popup_image', '', 'File gambar popup promo'),
('popup_link', '', 'Link tujuan klik gambar popup')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;
