<?php
// contact.php
session_start();
require_once 'includes/db.php';

$page_title = 'Kontak';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    // Validasi dasar
    if (empty($nama) || empty($email) || empty($pesan)) {
        $error_message = 'Mohon lengkapi semua field yang diperlukan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Format email tidak valid.';
    } else {
        // Opsi 1: Simpan ke database atau kirim email notifikasi (kode ditambahkan di sini)
        // ...

        // Opsi 2: Tampilkan pesan sukses dan alihkan ke WhatsApp
        $success_message = 'Terima kasih! Pesan Anda telah berhasil dikirim. Kami akan mengarahkan Anda ke WhatsApp untuk respons lebih cepat.';

        // Buat URL WhatsApp
        $whatsapp_message = "Halo, saya " . $nama . " (" . $email . ").\n\n" . $pesan;
        $whatsapp_url = "https://wa.me/628232020889?text=" . urlencode($whatsapp_message);

        // Alihkan pengguna ke WhatsApp setelah beberapa detik
        header("refresh:3;url=" . $whatsapp_url);
    }
}

include 'includes/header.php';
?>

<section class="hero-section text-white" style="min-height: 60vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="hero-title">Hubungi <span class="text-warning">Kami</span></h1>
                <p class="hero-subtitle">
                    Kami siap membantu Anda! Jangan ragu untuk menghubungi tim customer service kami.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success text-center" role="alert" data-aos="fade-up">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center" role="alert" data-aos="fade-up">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>


        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <h2 class="section-title text-dark">Informasi Kontak</h2>
                <p class="section-subtitle">Anda bisa menemukan kami di sini atau hubungi kami melalui platform yang tersedia.</p>

                <div class="d-flex align-items-start mb-4">
                    <div class="vm-icon me-3">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Alamat</h6>
                        <p class="text-muted mb-0">Kencana Green Residence</p>
                        <p class="text-muted mb-0">Cluster Green Park View Blok M9</p>
                        <p class="text-muted">Kota Bogor</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="vm-icon me-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Email</h6>
                        <p class="text-muted"><a href="mailto:info@irghareksajaya.com" class="text-reset text-decoration-none">info@irghareksajaya.com</a></p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="vm-icon me-3">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Telepon / WhatsApp</h6>
                        <p class="text-muted mb-0"><a href="https://02512028717" target="_blank" class="text-reset text-decoration-none">0251-2028-717</a></p>
                        <p class="text-muted mb-0"><a href="https://wa.me/6282320208899" target="_blank" class="text-reset text-decoration-none">+62 823-2020-8899</a></p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="vm-icon me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-dark">Jam Operasional</h6>
                        <p class="text-muted">Senin - Jumat: 08:00 - 17:00 WIB</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7" data-aos="fade-left">
                <div class="contact-form">
                    <h2 class="section-title text-dark">Kirim Pesan</h2>
                    <p class="section-subtitle">Isi formulir di bawah ini dan tim kami akan segera merespons.</p>
                    <form action="contact.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Anda" value="<?= htmlspecialchars($nama ?? '') ?>" required>
                            <label for="nama">Nama Lengkap</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Anda" value="<?= htmlspecialchars($email ?? '') ?>" required>
                            <label for="email">Alamat Email</label>
                        </div>
                        <div class="form-floating mb-4">
                            <textarea class="form-control" placeholder="Tuliskan pesan Anda di sini" id="pesan" name="pesan" style="height: 150px" required><?= htmlspecialchars($pesan ?? '') ?></textarea>
                            <label for="pesan">Pesan</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-5">
    <div class="container-fluid p-0" data-aos="fade-up">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.834249199341!2d106.7756309!3d-6.5424997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69c3e0e0000001%3A0x6a6a6a6a6a6a6a6a!2sKencana%20Green%20Residence!5e0!3m2!1sen!2sid!4v1678886400000!5m2!1sen!2sid"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>


<?php
include 'includes/footer.php';
?>