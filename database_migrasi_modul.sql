-- =======================================================
-- Migrasi: modul admin lengkap Heriest (portofolio, banner,
-- mitra, testimoni, faq, wa_clicks + pengaturan baru)
-- Cara pakai: import via phpMyAdmin ATAU biarkan tiap
-- halaman admin auto-migrasi (CREATE TABLE IF NOT EXISTS).
-- =======================================================

CREATE TABLE IF NOT EXISTS `portofolio` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150) NOT NULL,
  `klien` VARCHAR(100) NOT NULL DEFAULT '',
  `kategori_id` INT NULL,
  `lokasi` VARCHAR(100) NULL,
  `tahun` VARCHAR(4) NULL,
  `deskripsi` TEXT NULL,
  `gambar` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `banner_promo` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `headline` VARCHAR(200) NOT NULL,
  `deskripsi` TEXT NULL,
  `cta_text` VARCHAR(100) NOT NULL DEFAULT 'Cek Promo Sekarang',
  `cta_link` VARCHAR(255) NOT NULL DEFAULT '#produk',
  `gambar` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mitra` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_perusahaan` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) NULL,
  `link` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `testimoni` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `jabatan` VARCHAR(150) NULL,
  `isi` TEXT NOT NULL,
  `rating` TINYINT NOT NULL DEFAULT 5,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `faq` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pertanyaan` VARCHAR(255) NOT NULL,
  `jawaban` TEXT NOT NULL,
  `urutan` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wa_clicks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sumber` VARCHAR(50) NOT NULL DEFAULT 'umum',
  `ref_id` INT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (`sumber`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pengaturan baru (self-heal via upsert di admin, seed di sini)
INSERT INTO `pengaturan` (`setting_key`, `setting_value`, `label`) VALUES
('wa_template', 'Halo admin, saya ingin konsultasi kebutuhan advertising', 'Template Pesan WA Otomatis'),
('facebook', '#', 'Link Akun Facebook'),
('youtube', '#', 'Link Akun YouTube'),
('hero_badge', 'SOLUSI ADVERTISING TERBAIK UNTUK BISNIS ANDA', 'Badge Kecil Hero Section'),
('hero_headline', 'TINGKATKAN VISIBILITAS BISNIS ANDA BERSAMA KAMI!', 'Headline Hero Section'),
('hero_subdeskripsi', 'Solusi cetak, neon box, dan billboard profesional untuk segala kebutuhan promosi anda', 'Sub-Deskripsi Hero Section'),
('hero_cta_text', 'cek promo sekarang', 'Teks Tombol CTA Hero'),
('hero_cta_link', '#produk', 'Link Tombol CTA Hero')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;
