<?php
require_once __DIR__ . '/auth.php';

$db = getDB();
$error = '';
$msg = $_GET['msg'] ?? '';

// ---------- TESTIMONI ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['section'] ?? '') === 'testimoni') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $nama = trim($_POST['nama'] ?? '');
        $jabatan = trim($_POST['jabatan'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;
        if ($nama === '' || $isi === '') {
            $error = 'Nama dan isi testimoni tidak boleh kosong.';
        } else {
            try {
                if ($action === 'tambah') {
                    $stmt = $db->prepare("INSERT INTO testimoni (nama, jabatan, isi, rating, is_active) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nama, $jabatan ?: null, $isi, $rating, $is_active]);
                    header('Location: testimoni.php?msg=t_created#testimoni');
                    exit;
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    if ($id > 0) {
                        $stmt = $db->prepare("UPDATE testimoni SET nama = ?, jabatan = ?, isi = ?, rating = ?, is_active = ? WHERE id = ?");
                        $stmt->execute([$nama, $jabatan ?: null, $isi, $rating, $is_active, $id]);
                        header('Location: testimoni.php?msg=t_updated#testimoni');
                        exit;
                    }
                }
            } catch (Exception $e) {
                $error = 'Gagal menyimpan testimoni: ' . $e->getMessage();
            }
        }
    }
}

if (($_GET['section'] ?? '') === 'testimoni' && ($_GET['action'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM testimoni WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: testimoni.php?msg=t_deleted#testimoni');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menghapus testimoni.';
        }
    }
}

// ---------- FAQ ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['section'] ?? '') === 'faq') {
    $action = $_POST['action'] ?? '';
    if ($action === 'tambah' || $action === 'edit') {
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $jawaban = trim($_POST['jawaban'] ?? '');
        $urutan = (int)($_POST['urutan'] ?? 0);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        if ($pertanyaan === '' || $jawaban === '') {
            $error = 'Pertanyaan dan jawaban tidak boleh kosong.';
        } else {
            try {
                if ($action === 'tambah') {
                    $stmt = $db->prepare("INSERT INTO faq (pertanyaan, jawaban, urutan, is_active) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$pertanyaan, $jawaban, $urutan, $is_active]);
                    header('Location: testimoni.php?msg=f_created#faq');
                    exit;
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    if ($id > 0) {
                        $stmt = $db->prepare("UPDATE faq SET pertanyaan = ?, jawaban = ?, urutan = ?, is_active = ? WHERE id = ?");
                        $stmt->execute([$pertanyaan, $jawaban, $urutan, $is_active, $id]);
                        header('Location: testimoni.php?msg=f_updated#faq');
                        exit;
                    }
                }
            } catch (Exception $e) {
                $error = 'Gagal menyimpan FAQ: ' . $e->getMessage();
            }
        }
    }
}

if (($_GET['section'] ?? '') === 'faq' && ($_GET['action'] ?? '') === 'hapus') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM faq WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: testimoni.php?msg=f_deleted#faq');
            exit;
        } catch (Exception $e) {
            $error = 'Gagal menghapus FAQ.';
        }
    }
}

// ---------- DATA ----------
$testimonis = [];
$faqs = [];
if ($db) {
    $testimonis = $db->query("SELECT * FROM testimoni ORDER BY id DESC")->fetchAll();
    $faqs = $db->query("SELECT * FROM faq ORDER BY urutan ASC, id ASC")->fetchAll();
}

$page_title = "Testimoni & FAQ";
require_once __DIR__ . '/includes/header.php';

function flashBox($text, $color, $icon) {
    return '<div class="bg-' . $color . '-950/80 border border-' . $color . '-800 text-' . $color . '-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">'
        . '<i data-lucide="' . $icon . '" class="w-5 h-5 text-' . $color . '-400"></i>'
        . '<span>' . $text . '</span></div>';
}
$flashes = [
    't_created' => ['Testimoni baru berhasil ditambahkan!', 'emerald', 'check-circle'],
    't_updated' => ['Testimoni berhasil diperbarui!', 'sky', 'check-circle'],
    't_deleted' => ['Testimoni berhasil dihapus.', 'red', 'trash-2'],
    'f_created' => ['FAQ baru berhasil ditambahkan!', 'emerald', 'check-circle'],
    'f_updated' => ['FAQ berhasil diperbarui!', 'sky', 'check-circle'],
    'f_deleted' => ['FAQ berhasil dihapus.', 'red', 'trash-2'],
];
$inputCls = "w-full bg-[#0d0f17] border border-slate-700/80 focus:border-red-500 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 outline-none transition-all";
?>

<div class="space-y-10">

    <?php if (isset($flashes[$msg])): ?>
        <?= flashBox($flashes[$msg][0], $flashes[$msg][1], $flashes[$msg][2]) ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-950/80 border border-red-800 text-red-300 text-xs sm:text-sm rounded-2xl p-4 flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- ============ SECTION: TESTIMONI ============ -->
    <section id="testimoni" class="scroll-mt-20">
        <div class="flex items-center gap-2.5 mb-5">
            <i data-lucide="messages-square" class="w-5 h-5 text-red-500"></i>
            <h2 class="font-black text-base sm:text-lg text-white">Testimoni Pelanggan</h2>
            <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">Total: <?= count($testimonis) ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Form Tambah Testimoni -->
            <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-red-500"></i>
                    <span>Tambah Testimoni</span>
                </h3>
                <p class="text-xs text-slate-400 mb-5">Ulasan pelanggan yang tampil di web</p>

                <form action="testimoni.php#testimoni" method="POST" class="space-y-4">
                    <input type="hidden" name="section" value="testimoni">
                    <input type="hidden" name="action" value="tambah">

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="t_nama">Nama</label>
                        <input type="text" name="nama" id="t_nama" required placeholder="Contoh: Budi Santoso" class="<?= $inputCls ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="t_jabatan">Jabatan / Perusahaan</label>
                        <input type="text" name="jabatan" id="t_jabatan" placeholder="Contoh: Owner Kopi Nusa" class="<?= $inputCls ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="t_isi">Isi Testimoni</label>
                        <textarea name="isi" id="t_isi" required rows="3" placeholder="Tulis ulasan pelanggan..." class="<?= $inputCls ?>"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="t_rating">Rating</label>
                            <select name="rating" id="t_rating" class="<?= $inputCls ?>">
                                <?php for ($r = 5; $r >= 1; $r--): ?>
                                    <option value="<?= $r ?>"><?= $r ?> Bintang</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-red-600">
                                <span>Tampilkan</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Simpan Testimoni</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Testimoni -->
            <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
                <div class="pb-4 border-b border-slate-800">
                    <h3 class="font-bold text-sm text-white">Daftar Testimoni</h3>
                    <p class="text-xs text-slate-400">Semua ulasan pelanggan yang tersimpan</p>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                                <th class="py-3 px-3">#</th>
                                <th class="py-3 px-3">Nama</th>
                                <th class="py-3 px-3">Isi</th>
                                <th class="py-3 px-3 text-center">Rating</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            <?php if (empty($testimonis)): ?>
                                <tr><td colspan="6" class="py-8 text-center text-slate-500">Belum ada testimoni.</td></tr>
                            <?php else: ?>
                                <?php foreach ($testimonis as $idx => $t): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-3 text-slate-500 font-mono text-[11px]"><?= $idx + 1 ?></td>
                                    <td class="py-3 px-3">
                                        <p class="font-bold text-white"><?= htmlspecialchars($t['nama']) ?></p>
                                        <?php if (!empty($t['jabatan'])): ?>
                                            <p class="text-[11px] text-slate-500"><?= htmlspecialchars($t['jabatan']) ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 max-w-[220px]">
                                        <p class="truncate text-slate-400"><?= htmlspecialchars($t['isi']) ?></p>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="text-amber-400 font-bold whitespace-nowrap"><?= str_repeat('★', (int)$t['rating']) ?><span class="text-slate-600"><?= str_repeat('★', 5 - (int)$t['rating']) ?></span></span>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <?php if ((int)$t['is_active'] === 1): ?>
                                            <span class="bg-emerald-950/80 text-emerald-400 border border-emerald-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">Aktif</span>
                                        <?php else: ?>
                                            <span class="bg-slate-800 text-slate-400 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                onclick='editTestimoni(<?= (int)$t['id'] ?>, <?= json_encode($t['nama'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($t['jabatan'] ?? '', JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($t['isi'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$t['rating'] ?>, <?= (int)$t['is_active'] ?>)'
                                                class="p-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg transition-all"
                                                title="Edit Testimoni">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                            </button>
                                            <a href="testimoni.php?section=testimoni&action=hapus&id=<?= (int)$t['id'] ?>"
                                                onclick="return confirm('Yakin ingin menghapus testimoni ini?');"
                                                class="p-1.5 bg-slate-800 hover:bg-red-950/80 rounded-lg transition-all"
                                                title="Hapus Testimoni">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-400"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ SECTION: FAQ ============ -->
    <section id="faq" class="scroll-mt-20">
        <div class="flex items-center gap-2.5 mb-5">
            <i data-lucide="help-circle" class="w-5 h-5 text-red-500"></i>
            <h2 class="font-black text-base sm:text-lg text-white">FAQ / Tanya Jawab</h2>
            <span class="text-xs text-slate-400 font-semibold bg-slate-800 px-3 py-1 rounded-full">Total: <?= count($faqs) ?></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Form Tambah FAQ -->
            <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-sm text-white mb-1 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-red-500"></i>
                    <span>Tambah FAQ</span>
                </h3>
                <p class="text-xs text-slate-400 mb-5">Pertanyaan umum beserta jawabannya</p>

                <form action="testimoni.php#faq" method="POST" class="space-y-4">
                    <input type="hidden" name="section" value="faq">
                    <input type="hidden" name="action" value="tambah">

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="f_pertanyaan">Pertanyaan</label>
                        <input type="text" name="pertanyaan" id="f_pertanyaan" required placeholder="Contoh: Berapa lama proses cetak?" class="<?= $inputCls ?>">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="f_jawaban">Jawaban</label>
                        <textarea name="jawaban" id="f_jawaban" required rows="3" placeholder="Tulis jawaban..." class="<?= $inputCls ?>"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="f_urutan">Urutan</label>
                            <input type="number" name="urutan" id="f_urutan" value="0" min="0" class="<?= $inputCls ?>">
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-red-600">
                                <span>Tampilkan</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 active:scale-95 text-white font-bold text-xs py-2.5 rounded-xl shadow transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Simpan FAQ</span>
                    </button>
                </form>
            </div>

            <!-- Tabel FAQ -->
            <div class="lg:col-span-2 bg-[#141622] border border-slate-800 rounded-3xl p-6 shadow-sm">
                <div class="pb-4 border-b border-slate-800">
                    <h3 class="font-bold text-sm text-white">Daftar FAQ</h3>
                    <p class="text-xs text-slate-400">Diurutkan berdasarkan kolom urutan</p>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 font-semibold text-[11px] uppercase tracking-wider">
                                <th class="py-3 px-3 text-center">Urutan</th>
                                <th class="py-3 px-3">Pertanyaan</th>
                                <th class="py-3 px-3">Jawaban</th>
                                <th class="py-3 px-3 text-center">Status</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-300">
                            <?php if (empty($faqs)): ?>
                                <tr><td colspan="5" class="py-8 text-center text-slate-500">Belum ada FAQ.</td></tr>
                            <?php else: ?>
                                <?php foreach ($faqs as $f): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 px-3 text-center">
                                        <span class="bg-slate-800 text-slate-300 px-2.5 py-0.5 rounded-full text-xs font-semibold font-mono"><?= (int)$f['urutan'] ?></span>
                                    </td>
                                    <td class="py-3 px-3 font-bold text-white max-w-[200px]">
                                        <p class="truncate"><?= htmlspecialchars($f['pertanyaan']) ?></p>
                                    </td>
                                    <td class="py-3 px-3 max-w-[220px]">
                                        <p class="truncate text-slate-400"><?= htmlspecialchars($f['jawaban']) ?></p>
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <?php if ((int)$f['is_active'] === 1): ?>
                                            <span class="bg-emerald-950/80 text-emerald-400 border border-emerald-800 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">Aktif</span>
                                        <?php else: ?>
                                            <span class="bg-slate-800 text-slate-400 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                onclick='editFaq(<?= (int)$f['id'] ?>, <?= json_encode($f['pertanyaan'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($f['jawaban'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= (int)$f['urutan'] ?>, <?= (int)$f['is_active'] ?>)'
                                                class="p-1.5 bg-slate-800 hover:bg-slate-700 rounded-lg transition-all"
                                                title="Edit FAQ">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5 text-sky-400"></i>
                                            </button>
                                            <a href="testimoni.php?section=faq&action=hapus&id=<?= (int)$f['id'] ?>"
                                                onclick="return confirm('Yakin ingin menghapus FAQ ini?');"
                                                class="p-1.5 bg-slate-800 hover:bg-red-950/80 rounded-lg transition-all"
                                                title="Hapus FAQ">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-400"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Modal Edit Testimoni -->
<div id="modal-edit-testimoni" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-base text-white mb-1">Edit Testimoni</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah ulasan pelanggan</p>

        <form action="testimoni.php#testimoni" method="POST" class="space-y-4">
            <input type="hidden" name="section" value="testimoni">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="et_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nama</label>
                <input type="text" name="nama" id="et_nama" required class="<?= $inputCls ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Jabatan / Perusahaan</label>
                <input type="text" name="jabatan" id="et_jabatan" class="<?= $inputCls ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Isi Testimoni</label>
                <textarea name="isi" id="et_isi" required rows="3" class="<?= $inputCls ?>"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Rating</label>
                    <select name="rating" id="et_rating" class="<?= $inputCls ?>">
                        <?php for ($r = 5; $r >= 1; $r--): ?>
                            <option value="<?= $r ?>"><?= $r ?> Bintang</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" id="et_active" value="1" class="w-4 h-4 accent-red-600">
                        <span>Tampilkan</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-testimoni').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-2 rounded-xl shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit FAQ -->
<div id="modal-edit-faq" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-[#141622] border border-slate-800 rounded-3xl p-6 max-w-md w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-base text-white mb-1">Edit FAQ</h3>
        <p class="text-xs text-slate-400 mb-4">Ubah pertanyaan & jawaban</p>

        <form action="testimoni.php#faq" method="POST" class="space-y-4">
            <input type="hidden" name="section" value="faq">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="ef_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Pertanyaan</label>
                <input type="text" name="pertanyaan" id="ef_pertanyaan" required class="<?= $inputCls ?>">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Jawaban</label>
                <textarea name="jawaban" id="ef_jawaban" required rows="3" class="<?= $inputCls ?>"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Urutan</label>
                    <input type="number" name="urutan" id="ef_urutan" min="0" class="<?= $inputCls ?>">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" id="ef_active" value="1" class="w-4 h-4 accent-red-600">
                        <span>Tampilkan</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2.5 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-faq').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-700 text-xs font-semibold text-slate-300 hover:bg-slate-800">Batal</button>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-5 py-2 rounded-xl shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTestimoni(id, nama, jabatan, isi, rating, isActive) {
    document.getElementById('et_id').value = id;
    document.getElementById('et_nama').value = nama;
    document.getElementById('et_jabatan').value = jabatan;
    document.getElementById('et_isi').value = isi;
    document.getElementById('et_rating').value = rating;
    document.getElementById('et_active').checked = isActive === 1;
    document.getElementById('modal-edit-testimoni').classList.remove('hidden');
}
function editFaq(id, pertanyaan, jawaban, urutan, isActive) {
    document.getElementById('ef_id').value = id;
    document.getElementById('ef_pertanyaan').value = pertanyaan;
    document.getElementById('ef_jawaban').value = jawaban;
    document.getElementById('ef_urutan').value = urutan;
    document.getElementById('ef_active').checked = isActive === 1;
    document.getElementById('modal-edit-faq').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
