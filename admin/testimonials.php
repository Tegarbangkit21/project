<?php
// admin/testimonials.php
session_start();
require_once '../includes/db.php';

// Fungsi requireLogin() sekarang akan ditemukan
requireLogin();

$success_message = '';
$error_message = '';
$upload_dir = '../assets/images/testimonials/';

// Pastikan folder upload ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Handle form submissions (Add, Edit, Delete) - SEMUA DIJADIKAN SATU BLOK POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tindakan: Tambah Testimoni
    if (isset($_POST['add_testimonial'])) {
        $nama_pelanggan = trim($_POST['nama_pelanggan']);
        $komentar = trim($_POST['komentar']);
        // 1. Letakkan variabel $rating di sini
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;

        if (empty($nama_pelanggan) || empty($komentar)) {
            $error_message = 'Nama pelanggan dan komentar harus diisi.';
        } else {
            $foto = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $file_extension = strtolower(pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION));
                $file_name = 'testi_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
                $target_file = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    $foto = $file_name;
                } else {
                    $error_message = "Gagal mengupload foto.";
                }
            }
            if (empty($error_message)) {
                // 2. Ganti query INSERT Anda dengan yang ini
                $stmt = $pdo->prepare("INSERT INTO testimoni (nama_pelanggan, komentar, rating, foto, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($stmt->execute([$nama_pelanggan, $komentar, $rating, $foto])) {
                    $success_message = 'Testimoni berhasil ditambahkan.';
                } else {
                    $error_message = 'Gagal menyimpan testimoni.';
                }
            }
        }
    }

    // Tindakan: Edit Testimoni
    elseif (isset($_POST['edit_testimonial'])) {
        $id = (int)$_POST['id'];
        $nama_pelanggan = trim($_POST['nama_pelanggan']);
        $komentar = trim($_POST['komentar']);
        // 3. Letakkan variabel $rating di sini juga
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;

        if (empty($nama_pelanggan) || empty($komentar)) {
            $error_message = 'Nama pelanggan dan komentar harus diisi.';
        } else {
            $stmt = $pdo->prepare("SELECT foto FROM testimoni WHERE id = ?");
            $stmt->execute([$id]);
            $foto_lama = $stmt->fetchColumn();
            $foto = $foto_lama;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $file_name = 'testi_' . time() . '_' . basename($_FILES["foto"]["name"]);
                $target_file = $upload_dir . $file_name;
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    if ($foto_lama && file_exists($upload_dir . $foto_lama)) {
                        unlink($upload_dir . $foto_lama);
                    }
                    $foto = $file_name;
                } else {
                    $error_message = "Gagal mengupload foto baru.";
                }
            }
            if (empty($error_message)) {
                // 4. Ganti query UPDATE Anda dengan yang ini
                $stmt = $pdo->prepare("UPDATE testimoni SET nama_pelanggan = ?, komentar = ?, rating = ?, foto = ? WHERE id = ?");
                if ($stmt->execute([$nama_pelanggan, $komentar, $rating, $foto, $id])) {
                    $success_message = 'Testimoni berhasil diupdate.';
                } else {
                    $error_message = 'Gagal mengupdate testimoni.';
                }
            }
        }
    }

    // Tindakan: Hapus Testimoni (dipindahkan ke POST)
    elseif (isset($_POST['delete_testimonial'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT foto FROM testimoni WHERE id = ?");
        $stmt->execute([$id]);
        $foto = $stmt->fetchColumn();

        $stmt = $pdo->prepare("DELETE FROM testimoni WHERE id = ?");
        if ($stmt->execute([$id])) {
            if ($foto && file_exists($upload_dir . $foto)) {
                unlink($upload_dir . $foto);
            }
            $success_message = "Testimoni berhasil dihapus.";
        } else {
            $error_message = "Gagal menghapus testimoni.";
        }
    }
}


// Ambil semua data testimoni
$testimonials = $pdo->query("SELECT * FROM testimoni ORDER BY id DESC")->fetchAll();

$page_title = 'Kelola Testimoni';
include 'includes/admin_header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-comment-alt me-2"></i>Kelola Testimoni</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testimonialModal" onclick="resetModal()">
            <i class="fas fa-plus"></i> Tambah Testimoni
        </button>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= h($success_message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= h($error_message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($testimonials)): ?>
            <div class="col-12">
                <div class="text-center p-5 bg-light rounded">
                    <p class="text-muted">Belum ada testimoni. Silakan tambahkan testimoni baru.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($testimonials as $testi): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <?php if ($testi['foto'] && file_exists($upload_dir . $testi['foto'])): ?>
                                <img src="../assets/images/testimonials/<?= h($testi['foto']) ?>" class="rounded-circle mb-3" alt="<?= h($testi['nama_pelanggan']) ?>" style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle mb-3 bg-secondary d-inline-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="card-title"><?= h($testi['nama_pelanggan']) ?></h5>
                            <p class="card-text text-muted">"<?= h($testi['komentar']) ?>"</p>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-sm btn-outline-primary" onclick="editTestimonial(<?= htmlspecialchars(json_encode($testi), ENT_QUOTES, 'UTF-8') ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus testimoni ini?');">
                                <input type="hidden" name="id" value="<?= $testi['id'] ?>">
                                <button type="submit" name="delete_testimonial" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="testimonialModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Testimoni Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="testimonialId">
                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label for="komentar" class="form-label">Komentar</label>
                        <textarea class="form-control" id="komentar" name="komentar" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="komentar" class="form-label">Komentar</label>
                        <textarea class="form-control" id="komentar" name="komentar" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5)</label>
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="5">5 Bintang (Sangat Baik)</option>
                            <option value="4">4 Bintang (Baik)</option>
                            <option value="3">3 Bintang (Cukup)</option>
                            <option value="2">2 Bintang (Kurang)</option>
                            <option value="1">1 Bintang (Buruk)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto (Opsional)</label>
                        <input class="form-control" type="file" id="foto" name="foto" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>
                    <div class="text-center">
                        <img id="imagePreview" src="#" alt="Preview Foto" class="rounded-circle" style="display:none; width: 100px; height: 100px; object-fit: cover;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="submitButton" name="add_testimonial" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extra_js = '
<script>
function resetModal() {
    const form = document.querySelector("#testimonialModal form");
    form.reset();
    document.getElementById("modalTitle").textContent = "Tambah Testimoni Baru";
    document.getElementById("testimonialId").value = "";
    document.getElementById("imagePreview").style.display = "none";
    
    const submitBtn = document.getElementById("submitButton");
    submitBtn.textContent = "Simpan";
    submitBtn.name = "add_testimonial";
}

function editTestimonial(testi) {
    const form = document.querySelector("#testimonialModal form");
    form.reset();
    document.getElementById("modalTitle").textContent = "Edit Testimoni";
    document.getElementById("testimonialId").value = testi.id;
    document.getElementById("nama_pelanggan").value = testi.nama_pelanggan;
    document.getElementById("komentar").value = testi.komentar;
    
    const preview = document.getElementById("imagePreview");
    if (testi.foto) {
        // Path disesuaikan dengan lokasi file
        preview.src = `../assets/images/testimonials/${testi.foto}`;
        preview.style.display = "block";
    } else {
        preview.style.display = "none";
    }
    
    const submitBtn = document.getElementById("submitButton");
    submitBtn.textContent = "Update";
    submitBtn.name = "edit_testimonial";
    
    var myModal = new bootstrap.Modal(document.getElementById("testimonialModal"));
    myModal.show();
}

document.getElementById("foto").addEventListener("change", function(event) {
    const preview = document.getElementById("imagePreview");
    const file = event.target.files[0];
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";
    } else {
        preview.style.display = "none";
    }
});
</script>
';
include 'includes/admin_footer.php';
?>