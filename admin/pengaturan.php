<?php
$page_title = "Pengaturan Web";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Update Pengaturan Umum
    if ($action === 'pengaturan_umum') {
        $settings = [
            'nama_toko' => trim($_POST['nama_toko'] ?? ''),
            'tagline'   => trim($_POST['tagline'] ?? ''),
            'whatsapp'  => trim($_POST['whatsapp'] ?? ''),
            'wa_template' => trim($_POST['wa_template'] ?? 'Halo admin, saya ingin konsultasi kebutuhan advertising'),
            'email'     => trim($_POST['email'] ?? ''),
            'alamat'    => trim($_POST['alamat'] ?? ''),
            'tiktok'    => trim($_POST['tiktok'] ?? '#'),
            'instagram' => trim($_POST['instagram'] ?? '#'),
            'facebook'  => trim($_POST['facebook'] ?? '#'),
            'youtube'   => trim($_POST['youtube'] ?? '#'),
        ];

        try {
            $stmt = $db->prepare("
                INSERT INTO pengaturan (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
            ");
            foreach ($settings as $key => $val) {
                $stmt->execute([$key, $val]);
            }
            header('Location: pengaturan.php?msg=updated');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
        }
    }

    // Update Popup Promo / Pengumuman
    if ($action === 'popup_promo') {
        $popup_enabled = isset($_POST['popup_enabled']) ? '1' : '0';
        $popup_link = trim($_POST['popup_link'] ?? '');
        $upload_dir = __DIR__ . '/../assets/images/uploads/';
        $popup_image = getSetting('popup_image', '');
        $hapus_gambar = isset($_POST['hapus_gambar_popup']);

        try {
            // Hapus gambar lama jika dicentang
            if ($hapus_gambar && !empty($popup_image)) {
                if (file_exists($upload_dir . $popup_image)) {
                    unlink($upload_dir . $popup_image);
                }
                $popup_image = '';
            }

            // Upload gambar baru jika ada
            if (isset($_FILES['popup_image']) && $_FILES['popup_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['popup_image']['tmp_name'];
                $file_name = $_FILES['popup_image']['name'];
                $file_size = $_FILES['popup_image']['size'];
                $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($ext, $allowed_exts)) {
                    $error = 'Format gambar popup hanya boleh JPG, JPEG, PNG, atau WEBP.';
                } elseif ($file_size > 5 * 1024 * 1024) {
                    $error = 'Ukuran gambar popup maksimal 5MB.';
                } else {
                    if (!empty($popup_image) && file_exists($upload_dir . $popup_image)) {
                        unlink($upload_dir . $popup_image);
                    }
                    $popup_image = 'popup_' . time() . '.' . $ext;
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    move_uploaded_file($file_tmp, $upload_dir . $popup_image);
                }
            }

            if (empty($error)) {
                $stmt = $db->prepare("
                    INSERT INTO pengaturan (setting_key, setting_value)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute(['popup_enabled', $popup_enabled]);
                $stmt->execute(['popup_link', $popup_link]);
                $stmt->execute(['popup_image', $popup_image]);
                header('Location: pengaturan.php?msg=popup_updated');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Gagal menyimpan popup promo: ' . $e->getMessage();
        }
    }

    // Update Hero Section (headline, deskripsi, CTA)
    if ($action === 'hero_section') {
        $settings = [
            'hero_badge' => trim($_POST['hero_badge'] ?? ''),
            'hero_headline' => trim($_POST['hero_headline'] ?? ''),
            'hero_subdeskripsi' => trim($_POST['hero_subdeskripsi'] ?? ''),
            'hero_cta_text' => trim($_POST['hero_cta_text'] ?? 'cek promo sekarang'),
            'hero_cta_link' => trim($_POST['hero_cta_link'] ?? '#produk'),
        ];

        try {
            $stmt = $db->prepare(
                "INSERT INTO pengaturan (setting_key, setting_value)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
            );
            foreach ($settings as $key => $val) {
                $stmt->execute([$key, $val]);
            }
            header('Location: pengaturan.php?msg=hero_updated');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menyimpan hero section: ' . $e->getMessage();
        }
    }

    // Update Password Admin
    if ($action === 'ganti_password') {
        $pass_lama = $_POST['pass_lama'] ?? '';
        $pass_baru = $_POST['pass_baru'] ?? '';
        $pass_konf = $_POST['pass_konf'] ?? '';

        if (empty($pass_lama) || empty($pass_baru)) {
            $error = 'Semua kolom password wajib diisi.';
        } elseif ($pass_baru !== $pass_konf) {
            $error = 'Konfirmasi password baru tidak cocok.';
        } elseif (strlen($pass_baru) < 6) {
            $error = 'Password baru minimal 6 karakter.';
        } else {
            $admin_id = $_SESSION['admin_id'];
            $stmt = $db->prepare("SELECT password FROM admins WHERE id = ? LIMIT 1");
            $stmt->execute([$admin_id]);
            $current_hash = $stmt->fetchColumn();

            if ($current_hash && password_verify($pass_lama, $current_hash)) {
                $new_hash = password_hash($pass_baru, PASSWORD_BCRYPT);
                $upd = $db->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $upd->execute([$new_hash, $admin_id]);
                header('Location: pengaturan.php?msg=pass_updated');
                exit;
            } else {
                $error = 'Password lama Anda salah.';
            }
        }
    }
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Header Page -->
    <div>
        <h2 class="text-lg sm:text-xl font-black text-white">Pengaturan Website</h2>
        <p class="text-xs text-slate-400">Sesuaikan informasi kontak, alamat toko, dan profil administrator</p>
    </div>

    <!-- Flash Messages -->
    <?php if ($msg === 'updated'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Pengaturan website berhasil disimpan!</span>
        </div>
    <?php elseif ($msg === 'pass_updated'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Password admin berhasil diperbarui!</span>
        </div>
    <?php elseif ($msg === 'popup_updated'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Popup promo berhasil disimpan!</span>
        </div>
    <?php elseif ($msg === 'hero_updated'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Hero section berhasil disimpan!</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Form Pengaturan Umum -->
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        
        <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
            <i data-lucide="store" class="w-4 h-4 text-red-500"></i>
            <span>Informasi Kontak & Bisnis</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Data ini otomatis tampil di header, tombol WhatsApp, dan footer website.</p>

        <form action="pengaturan.php" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="pengaturan_umum">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="nama_toko">Nama Perusahaan / Brand</label>
                    <input 
                        type="text" 
                        name="nama_toko" 
                        id="nama_toko"
                        value="<?= htmlspecialchars(getSetting('nama_toko', 'HERIEST CV. ADVERTISING')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="tagline">Tagline Brand</label>
                    <input 
                        type="text" 
                        name="tagline" 
                        id="tagline"
                        value="<?= htmlspecialchars(getSetting('tagline', 'We Touch With Colours.')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="whatsapp">Nomor WhatsApp Utama</label>
                    <div class="relative flex items-center">
                        <i data-lucide="phone" class="w-4 h-4 text-emerald-400 absolute left-3.5"></i>
                        <input 
                            type="text" 
                            name="whatsapp" 
                            id="whatsapp"
                            placeholder="082264356885"
                            value="<?= htmlspecialchars(getSetting('whatsapp', '082264356885')) ?>"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                        >
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Gunakan format 08... atau 628...</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="email">Alamat Email</label>
                    <div class="relative flex items-center">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-500 absolute left-3.5"></i>
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            placeholder="info@heriest.com"
                            value="<?= htmlspecialchars(getSetting('email', 'info@heriest.com')) ?>"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                        >
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="wa_template">Template Pesan WhatsApp Otomatis</label>
                    <textarea
                        name="wa_template"
                        id="wa_template"
                        rows="2"
                        placeholder="Halo admin, saya ingin konsultasi kebutuhan advertising"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white outline-none"
                    ><?= htmlspecialchars(getSetting('wa_template', 'Halo admin, saya ingin konsultasi kebutuhan advertising')) ?></textarea>
                    <p class="text-[11px] text-slate-500 mt-1">Pesan default tombol Konsultasi & CTA umum di website.</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="alamat">Alamat Workshop / Kantor</label>
                    <textarea
                        name="alamat"
                        id="alamat"
                        rows="2"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white outline-none"
                    ><?= htmlspecialchars(getSetting('alamat', 'Jl. kabupaten nomor mangunsari, rego, kabupaten, manandai, kabupaten, kabupaten karanganyar, Jawa Timur 63281')) ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="facebook">Link Facebook</label>
                    <input
                        type="text"
                        name="facebook"
                        id="facebook"
                        placeholder="#"
                        value="<?= htmlspecialchars(getSetting('facebook', '#')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="youtube">Link YouTube</label>
                    <input
                        type="text"
                        name="youtube"
                        id="youtube"
                        placeholder="#"
                        value="<?= htmlspecialchars(getSetting('youtube', '#')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                    >
                </div>

            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button 
                    type="submit" 
                    class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow transition-all flex items-center gap-2"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>

        </form>

    </div>

    <!-- Form Hero Section (Headline & CTA) -->
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">

        <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
            <i data-lucide="layout-template" class="w-4 h-4 text-sky-500"></i>
            <span>Hero Section (Banner Utama)</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Ubah headline, deskripsi promo, & link tombol di halaman depan website.</p>

        <form action="pengaturan.php" method="POST" class="space-y-5">
            <input type="hidden" name="action" value="hero_section">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="hero_badge">Badge Kecil di Atas Headline</label>
                    <input
                        type="text" name="hero_badge" id="hero_badge"
                        value="<?= htmlspecialchars(getSetting('hero_badge', 'SOLUSI ADVERTISING TERBAIK UNTUK BISNIS ANDA')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="hero_headline">Headline Utama</label>
                    <textarea name="hero_headline" id="hero_headline" rows="2"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white outline-none"
                    ><?= htmlspecialchars(getSetting('hero_headline', 'TINGKATKAN VISIBILITAS BISNIS ANDA BERSAMA KAMI!')) ?></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="hero_subdeskripsi">Deskripsi Promo di Bawah Headline</label>
                    <textarea name="hero_subdeskripsi" id="hero_subdeskripsi" rows="2"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl p-4 text-xs sm:text-sm text-white outline-none"
                    ><?= htmlspecialchars(getSetting('hero_subdeskripsi', 'Solusi cetak, neon box, dan billboard profesional untuk segala kebutuhan promosi anda')) ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="hero_cta_text">Teks Tombol CTA</label>
                    <input
                        type="text" name="hero_cta_text" id="hero_cta_text"
                        value="<?= htmlspecialchars(getSetting('hero_cta_text', 'cek promo sekarang')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="hero_cta_link">Link Tombol CTA</label>
                    <input
                        type="text" name="hero_cta_link" id="hero_cta_link"
                        value="<?= htmlspecialchars(getSetting('hero_cta_link', '#produk')) ?>"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow transition-all flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Hero</span>
                </button>
            </div>
        </form>

    </div>

    <!-- Form Popup Promo / Pengumuman -->
    <?php
        $popup_enabled_val = getSetting('popup_enabled', '0');
        $popup_image_val = getSetting('popup_image', '');
        $popup_link_val = getSetting('popup_link', '');
        $popup_img_exists = !empty($popup_image_val) && file_exists(__DIR__ . '/../assets/images/uploads/' . $popup_image_val);
    ?>
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">

        <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
            <i data-lucide="megaphone" class="w-4 h-4 text-rose-500"></i>
            <span>Popup Promo / Pengumuman</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Gambar ini muncul otomatis sekali saat pengunjung baru membuka website. Bisa ditutup (close) oleh pengunjung.</p>

        <form action="pengaturan.php" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="action" value="popup_promo">

            <div class="flex items-center gap-3 bg-[#0d0f17] border border-slate-700/80 rounded-xl px-4 py-3">
                <input
                    type="checkbox"
                    name="popup_enabled"
                    id="popup_enabled"
                    value="1"
                    <?= $popup_enabled_val === '1' ? 'checked' : '' ?>
                    class="w-4 h-4 accent-red-600"
                >
                <label for="popup_enabled" class="text-xs sm:text-sm text-slate-200 font-semibold cursor-pointer">Tampilkan popup saat website dibuka</label>
            </div>

            <?php if ($popup_img_exists): ?>
                <div>
                    <p class="block text-xs font-semibold text-slate-300 mb-1.5">Gambar Aktif Saat Ini</p>
                    <img src="../assets/images/uploads/<?= htmlspecialchars($popup_image_val) ?>" alt="Popup promo aktif" class="max-w-xs w-full rounded-xl border border-slate-700 object-cover">
                    <label class="mt-2 flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="hapus_gambar_popup" value="1" class="w-4 h-4 accent-red-600">
                        <span>Hapus gambar ini (popup tidak tampil sampai gambar baru diupload)</span>
                    </label>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="popup_image">Upload Gambar Popup (JPG/PNG/WEBP, maks 5MB)</label>
                <input
                    type="file"
                    name="popup_image"
                    id="popup_image"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-300 outline-none file:mr-3 file:bg-slate-800 file:text-white file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-xs"
                >
                <p class="text-[11px] text-slate-500 mt-1">Disarankan gambar portrait/persegi (mis. 800x1000px). Upload baru menggantikan gambar lama.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="popup_link">Link Tujuan Saat Gambar Diklik (opsional)</label>
                <input
                    type="text"
                    name="popup_link"
                    id="popup_link"
                    placeholder="produk.php atau https://wa.me/628..."
                    value="<?= htmlspecialchars($popup_link_val) ?>"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
                <p class="text-[11px] text-slate-500 mt-1">Kosongkan jika gambar hanya info tanpa link.</p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow transition-all flex items-center gap-2"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Popup</span>
                </button>
            </div>

        </form>

    </div>

    <!-- Form Ganti Password -->
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        
        <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
            <i data-lucide="key" class="w-4 h-4 text-amber-500"></i>
            <span>Ganti Password Administrator</span>
        </h3>
        <p class="text-xs text-slate-400 mb-6">Perbarui kata sandi akun login administrator Anda</p>

        <form action="pengaturan.php" method="POST" class="space-y-4 max-w-md">
            <input type="hidden" name="action" value="ganti_password">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="pass_lama">Password Saat Ini</label>
                <input 
                    type="password" 
                    name="pass_lama" 
                    id="pass_lama"
                    required
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="pass_baru">Password Baru (Minimal 6 karakter)</label>
                <input 
                    type="password" 
                    name="pass_baru" 
                    id="pass_baru"
                    required
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="pass_konf">Ulangi Password Baru</label>
                <input 
                    type="password" 
                    name="pass_konf" 
                    id="pass_konf"
                    required
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none"
                >
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl border border-slate-700 shadow transition-all flex items-center gap-2"
                >
                    <i data-lucide="lock" class="w-4 h-4"></i>
                    <span>Perbarui Password</span>
                </button>
            </div>

        </form>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
