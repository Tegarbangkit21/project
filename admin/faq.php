<?php
// admin/faq.php
session_start();
require_once '../includes/db.php';

requireLogin();

$success_message = '';
$error_message = '';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pertanyaan = trim($_POST['pertanyaan']);
    $jawaban = trim($_POST['jawaban']);
    $kategori_faq = trim($_POST['kategori_faq']); // Ambil nilai kategori dari form
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if (empty($pertanyaan) || empty($jawaban) || empty($kategori_faq)) {
        $error_message = 'Pertanyaan, jawaban, dan kategori harus diisi.';
    } else {
        if ($id === 0) { // Add
            $stmt = $pdo->prepare("INSERT INTO faq (pertanyaan, jawaban, kategori_faq) VALUES (?, ?, ?)");
            if ($stmt->execute([$pertanyaan, $jawaban, $kategori_faq])) {
                $success_message = 'FAQ berhasil ditambahkan.';
            } else {
                $error_message = 'Gagal menyimpan FAQ.';
            }
        } else { // Edit
            $stmt = $pdo->prepare("UPDATE faq SET pertanyaan = ?, jawaban = ?, kategori_faq = ? WHERE id = ?");
            if ($stmt->execute([$pertanyaan, $jawaban, $kategori_faq, $id])) {
                $success_message = 'FAQ berhasil diupdate.';
            } else {
                $error_message = 'Gagal mengupdate FAQ.';
            }
        }
    }
}

// Handle Delete (dipindahkan ke method POST agar lebih aman)
if (isset($_POST['delete_faq'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM faq WHERE id = ?");
    if ($stmt->execute([$id])) {
        $success_message = "FAQ berhasil dihapus.";
    } else {
        $error_message = "Gagal menghapus FAQ.";
    }
}

// Ambil semua data FAQ
$faqs = $pdo->query("SELECT * FROM faq ORDER BY kategori_faq, id DESC")->fetchAll();

$page_title = 'Kelola FAQ';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-question-circle me-2"></i>Kelola FAQ</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="resetModal()">
            <i class="fas fa-plus"></i> Tambah FAQ
        </button>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= h($success_message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= h($error_message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="accordion" id="faqAccordion">
        <?php if (empty($faqs)): ?>
            <div class="text-center p-5 bg-light rounded">
                <p class="text-muted">Belum ada FAQ.</p>
            </div>
        <?php else: ?>
            <?php foreach ($faqs as $faq): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-<?= $faq['id'] ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $faq['id'] ?>">
                            <span class="badge bg-primary me-2"><?= h(ucfirst($faq['kategori_faq'])) ?></span> <?= h($faq['pertanyaan']) ?>
                        </button>
                    </h2>
                    <div id="collapse-<?= $faq['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p><?= nl2br(h($faq['jawaban'])) ?></p>
                            <hr>
                            <div class="text-end">
                                <button class="btn btn-sm btn-outline-primary" onclick="editFaq(<?= htmlspecialchars(json_encode($faq), ENT_QUOTES, 'UTF-8') ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus FAQ ini?');">
                                    <input type="hidden" name="id" value="<?= $faq['id'] ?>">
                                    <button type="submit" name="delete_faq" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah FAQ Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="faqId">
                    <div class="mb-3">
                        <label for="pertanyaan" class="form-label">Pertanyaan</label>
                        <input type="text" class="form-control" id="pertanyaan" name="pertanyaan" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori_faq" class="form-label">Kategori FAQ</label>
                        <select class="form-select" id="kategori_faq" name="kategori_faq" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="pemesanan">Pemesanan</option>
                            <option value="produk">Produk</option>
                            <option value="pengiriman">Pengiriman</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jawaban" class="form-label">Jawaban</label>
                        <textarea class="form-control" id="jawaban" name="jawaban" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extra_js = '
<script>
function resetModal() {
    document.querySelector("#faqModal form").reset();
    document.getElementById("modalTitle").textContent = "Tambah FAQ Baru";
    document.getElementById("faqId").value = "";
}

function editFaq(faq) {
    document.querySelector("#faqModal form").reset();
    document.getElementById("modalTitle").textContent = "Edit FAQ";
    document.getElementById("faqId").value = faq.id;
    document.getElementById("pertanyaan").value = faq.pertanyaan;
    document.getElementById("jawaban").value = faq.jawaban;
    document.getElementById("kategori_faq").value = faq.kategori_faq; // Mengisi nilai kategori
    
    var myModal = new bootstrap.Modal(document.getElementById("faqModal"));
    myModal.show();
}
</script>
';
include 'includes/admin_footer.php';
?>