<?php
/**
 * Halaman Login Admin - HERIEST ADVERTISING
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$msg = $_GET['msg'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Silakan isi username dan password.';
    } else {
        $db = getDB();
        if ($db) {
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } else {
            $error = 'Gagal terhubung ke database MySQL.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - HERIEST ADVERTISING</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/tailwind-admin.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="../assets/js/theme-init.js"></script>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page min-h-screen flex items-center justify-center p-4 text-slate-100 antialiased font-sans">

    <div class="w-full max-w-md">
        
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <a href="../index.php" class="inline-flex items-center gap-2 mb-2 group">
                <div class="bg-red-600 text-white font-black text-2xl px-3 py-1.5 rounded-lg flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    X
                </div>
                <div class="text-left">
                    <div class="font-black text-lg tracking-wider text-white leading-none">
                        HERIEST <span class="text-xs font-normal text-slate-400">CV.</span>
                    </div>
                    <div class="text-[9px] font-bold tracking-widest text-red-500 uppercase mt-0.5">
                        ADVERTISING
                    </div>
                </div>
            </a>
            <p class="text-yellow-400 font-brush text-xl">We Touch With Colours.</p>
            <h1 class="text-lg font-bold text-white mt-4">Panel Administrator</h1>
            <p class="text-xs text-slate-400">Masuk untuk mengelola katalog produk & konten website</p>
        </div>

        <!-- Login Card -->
        <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs rounded-xl p-3.5 mb-5 flex items-center gap-2.5">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-400 shrink-0"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($msg === 'logout_success'): ?>
                <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs rounded-xl p-3.5 mb-5 flex items-center gap-2.5">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                    <span>Anda telah berhasil logout.</span>
                </div>
            <?php elseif ($msg === 'required'): ?>
                <div class="bg-amber-950/80 border border-amber-800 text-amber-300 text-xs rounded-xl p-3.5 mb-5 flex items-center gap-2.5">
                    <i data-lucide="info" class="w-4 h-4 text-amber-400 shrink-0"></i>
                    <span>Silakan login terlebih dahulu untuk mengakses panel admin.</span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="username">Username</label>
                    <div class="relative flex items-center">
                        <i data-lucide="user" class="w-4 h-4 text-slate-500 absolute left-3.5"></i>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            required
                            placeholder="admin" 
                            value="admin"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 focus:ring-2 focus:ring-red-900/40 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="password">Password</label>
                    <div class="relative flex items-center">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-500 absolute left-3.5"></i>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required
                            placeholder="admin123" 
                            value="admin123"
                            class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 focus:ring-2 focus:ring-red-900/40 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all"
                        >
                    </div>
                </div>

                <div class="pt-2">
                    <button 
                        type="submit" 
                        class="w-full bg-red-600 hover:bg-red-700 active:scale-98 text-white font-bold text-xs sm:text-sm py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2"
                    >
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </div>

            </form>

            <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                <p class="text-[11px] text-slate-500">
                    Default Login: <strong class="text-slate-300">admin</strong> / Password: <strong class="text-slate-300">admin123</strong>
                </p>
                <div class="mt-4">
                    <a href="../index.php" class="text-xs text-slate-400 hover:text-white inline-flex items-center gap-1.5 transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                        <span>Kembali ke Halaman Utama</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Skrip login: inisialisasi ikon (fungsi lain di admin.js aman tanpa sidebar) -->
    <script src="../assets/js/admin.js"></script>
</body>
</html>
