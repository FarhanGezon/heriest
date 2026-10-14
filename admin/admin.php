<?php
$page_title = "Kelola Admin";
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';
$self_id = (int)($_SESSION['admin_id'] ?? 0);

// Tambah admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'tambah') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($username === '' || $nama === '' || $pass === '') {
        $error = 'Username, nama lengkap, dan password wajib diisi.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO admins (username, password, nama_lengkap, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, password_hash($pass, PASSWORD_BCRYPT), $nama, $email ?: null]);
            header('Location: admin.php?msg=created');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menambah admin (username mungkin sudah dipakai).';
        }
    }
}

// Update admin (nama + email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    if ($id > 0 && $nama !== '') {
        try {
            $stmt = $db->prepare("UPDATE admins SET nama_lengkap = ?, email = ? WHERE id = ?");
            $stmt->execute([$nama, $email ?: null, $id]);
            header('Location: admin.php?msg=updated');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal memperbarui admin.';
        }
    }
}

// Reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
    $id = (int)($_POST['id'] ?? 0);
    $pass = $_POST['password_baru'] ?? '';
    if ($id > 0 && strlen($pass) >= 6) {
        try {
            $stmt = $db->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->execute([password_hash($pass, PASSWORD_BCRYPT), $id]);
            header('Location: admin.php?msg=reset');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal mereset password.';
        }
    } else {
        $error = 'Password baru minimal 6 karakter.';
    }
}

// Hapus admin (tidak boleh hapus diri sendiri / satu-satunya)
if (isset($_GET['action']) && $_GET['action'] === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $id !== $self_id) {
        try {
            $total = (int)$db->query("SELECT COUNT(*) FROM admins")->fetchColumn();
            if ($total <= 1) {
                $error = 'Tidak bisa menghapus satu-satunya akun admin.';
            } else {
                $stmt = $db->prepare("DELETE FROM admins WHERE id = ?");
                $stmt->execute([$id]);
                header('Location: admin.php?msg=deleted');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Gagal menghapus admin.';
        }
    } elseif ($id === $self_id) {
        $error = 'Tidak bisa menghapus akun yang sedang dipakai login.';
    }
}

$admins = [];
if ($db) {
    $admins = $db->query("SELECT id, username, nama_lengkap, email, created_at FROM admins ORDER BY id ASC")->fetchAll();
}
require_once __DIR__ . '/includes/header.php';
?>

<div class="space-y-6">

    <?php if ($msg === 'created'): ?>
        <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
            <span>Akun admin baru berhasil ditambahkan!</span>
        </div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-sky-400"></i>
            <span>Data admin berhasil diperbarui!</span>
        </div>
    <?php elseif ($msg === 'reset'): ?>
        <div class="bg-sky-950/80 border border-sky-800 text-sky-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="key" class="w-5 h-5 text-sky-400"></i>
            <span>Password admin berhasil direset!</span>
        </div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="trash-2" class="w-5 h-5 text-red-400"></i>
            <span>Akun admin berhasil dihapus.</span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- Form Tambah Admin -->
        <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-4 h-4 text-red-500"></i>
                <span>Tambah Admin Baru</span>
            </h3>
            <p class="text-xs text-slate-400 mb-5">Buat akun login dashboard tambahan</p>

            <form action="admin.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="tambah">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Username *</label>
                    <input type="text" name="username" required placeholder="cth: admin2"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" required placeholder="cth: Budi Santoso"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>
                    <input type="email" name="email" placeholder="admin@heriest.com"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password * (min 6)</label>
                    <input type="password" name="password" required minlength="6" placeholder="••••••••"
                        class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Simpan Admin</span>
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Admin -->
        <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="font-bold text-sm text-white">Daftar Administrator</h3>
                    <p class="text-xs text-slate-400">Semua akun yang bisa login dashboard</p>
                </div>
                <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">
                    Total: <?= count($admins) ?>
                </span>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                            <th class="py-3 px-3">Akun</th>
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php foreach ($admins as $a): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-red-500 shrink-0">
                                        <?= strtoupper(substr($a['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white text-xs sm:text-sm">
                                            <?= htmlspecialchars($a['nama_lengkap']) ?>
                                            <?php if ((int)$a['id'] === $self_id): ?>
                                                <span class="ml-1 text-[10px] font-semibold text-emerald-400 bg-emerald-950/60 border border-emerald-900/60 px-2 py-0.5 rounded-full">Anda</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="text-[11px] text-slate-500 font-mono">@<?= htmlspecialchars($a['username']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-400"><?= htmlspecialchars($a['email'] ?? '-') ?></td>
                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="editAdmin(<?= (int)$a['id'] ?>, '<?= addslashes(htmlspecialchars($a['nama_lengkap'])) ?>', '<?= addslashes(htmlspecialchars($a['email'] ?? '')) ?>')"
                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all" title="Edit Data">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                    </button>
                                    <button onclick="resetAdmin(<?= (int)$a['id'] ?>, '<?= addslashes(htmlspecialchars($a['username'])) ?>')"
                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg transition-all" title="Reset Password">
                                        <i data-lucide="key" class="w-3.5 h-3.5 text-amber-400"></i>
                                    </button>
                                    <?php if ((int)$a['id'] !== $self_id): ?>
                                    <a href="admin.php?action=hapus&id=<?= (int)$a['id'] ?>"
                                        onclick="return confirm('Yakin hapus admin @<?= addslashes($a['username']) ?>?');"
                                        class="p-1.5 bg-slate-800 hover:bg-red-950/80 text-slate-400 hover:text-red-400 rounded-lg transition-all" title="Hapus Admin">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-400"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit-admin" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="font-bold text-base text-white mb-1">Edit Admin</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah nama & email akun</p>
        <form action="admin.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_admin_id">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="edit_admin_nama" required
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>
                <input type="email" name="email" id="edit_admin_email"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="tutupModalAdmin()"
                    class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-2 rounded-xl shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="modal-reset-admin" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl">
        <h3 class="font-bold text-base text-white mb-1">Reset Password</h3>
        <p class="text-xs text-slate-400 mb-4" id="reset_admin_label">Password baru min 6 karakter</p>
        <form action="admin.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="reset">
            <input type="hidden" name="id" id="reset_admin_id">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password Baru</label>
                <input type="password" name="password_baru" required minlength="6" placeholder="••••••••"
                    class="w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white outline-none">
            </div>
            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="tutupModalAdmin()"
                    class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800">Batal</button>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-5 py-2 rounded-xl shadow">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalAdmin(id) {
    var m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function tutupModalAdmin() {
    ['modal-edit-admin', 'modal-reset-admin'].forEach(function (id) {
        var m = document.getElementById(id);
        m.classList.add('hidden');
        m.classList.remove('flex');
    });
}
function editAdmin(id, nama, email) {
    document.getElementById('edit_admin_id').value = id;
    document.getElementById('edit_admin_nama').value = nama;
    document.getElementById('edit_admin_email').value = email;
    bukaModalAdmin('modal-edit-admin');
}
function resetAdmin(id, username) {
    document.getElementById('reset_admin_id').value = id;
    document.getElementById('reset_admin_label').textContent = 'Password baru untuk @' + username + ' (min 6 karakter)';
    bukaModalAdmin('modal-reset-admin');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
