<?php
// index.php
session_start();
require_once 'includes/db.php';

$page_title = 'Beranda';

// Ambil beberapa produk featured untuk ditampilkan
$stmt = $pdo->query("SELECT p.*, k.nama_kategori, s.nama_subkategori 
                     FROM produk p 
                     LEFT JOIN kategori k ON p.id_kategori = k.id 
                     LEFT JOIN subkategori s ON p.id_subkategori = s.id 
                     WHERE k.nama_kategori != 'Custom'
                     ORDER BY p.created_at DESC LIMIT 6");
$featured_products = $stmt->fetchAll();

// Ambil beberapa testimoni untuk ditampilkan
$stmt = $pdo->query("SELECT * FROM testimoni ORDER BY created_at DESC LIMIT 3");
$testimonials = $stmt->fetchAll();

include 'includes/header.php';
?>


<!-- Hero Section (Carousel) -->
<section class="hero-carousel">
    <div id="mainHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            <!-- Slide 1: Blue gradient -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: radial-gradient(1200px 600px at 80% -10%, rgba(255,255,255,.15), transparent), var(--gradient-primary);">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-7" data-aos="fade-right">
                                <span class="badge badge-silver mb-3 px-3 py-2 rounded-pill fw-semibold">PT. IRGHA REKSA JAYA</span>
                                <h1 class="hero-title text-white">CRUNCH it ONCE,<br />CRAVE IT FOREVER!</h1>
                                <p class="hero-subtitle text-white-50">Snack premium CHIBOR yang renyah & menggugah selera. Diproduksi dengan standar kualitas tinggi.</p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="products.php" class="btn btn-hero btn-hero-primary">Lihat Produk</a>
                                    <a href="contact.php" class="btn btn-hero btn-hero-outline">Hubungi Kami</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 2: Blue → Silver gradient -->
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('assets/images/cheese.png');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-7" data-aos="fade-up">
                                <span class="badge badge-silver mb-3 px-3 py-2 rounded-pill fw-semibold">Quality First</span>
                                <h2 class="text-white mb-3">HACCP • ISO 22000 • Halal</h2>
                                <p class="text-white-50">Keamanan dan mutu terjamin dari bahan baku hingga distribusi. Siap OEM & private label.</p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="about.php" class="btn btn-hero btn-hero-primary">Tentang Kami</a>
                                    <a href="faq.php" class="btn btn-hero btn-hero-outline">Lihat Sertifikasi</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Slide 3: Background image -->
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('assets/images/IRJ-OFFICE.png');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-7" data-aos="fade-right">
                                <span class="badge badge-silver mb-3 px-3 py-2 rounded-pill fw-semibold">Distribusi Luas</span>
                                <h2 class="text-white mb-3">Produk Selalu Siap</h2>
                                <p class="text-white-50">Jaringan ritel & distributor aktif, dukungan sales responsif, dan pengiriman tepat waktu.</p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <a href="products.php" class="btn btn-hero btn-hero-primary">Cek Katalog</a>
                                    <a href="contact.php" class="btn btn-hero btn-hero-outline">Minta Penawaran</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainHeroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#mainHeroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
    </div>
</section>

<!-- About / Difference -->
<section class="section">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
            <span class="badge badge-silver px-3 py-2 rounded-pill fw-semibold">Tentang Kami</span>
            <h2 class="section-title">Produsen Snack Premium — Biru & Silver yang Modern</h2>
            <p class="section-subtitle">Kami memproduksi snack renyah dan lezat dengan standar keamanan pangan tinggi (HACCP, ISO 22000, Halal). Siap OEM & private label untuk pasar domestik maupun ekspor.</p>
            <div class="values">
                <div class="value">
                    <div class="t">Kualitas Terjamin</div>
                    <div class="d">QC berlapis dan traceability batch untuk konsistensi rasa & keamanan.</div>
                </div>
                <div class="value">
                    <div class="t">R&D In‑house</div>
                    <div class="d">Formulasi resep khusus sesuai kebutuhan brand/ritel Anda.</div>
                </div>
                <div class="value">
                    <div class="t">Distribusi Luas</div>
                    <div class="d">Pengiriman tepat waktu dengan dukungan tim sales yang responsif.</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="cta">
                <div class="title">Siap Kolaborasi?</div>
                <p class="sub mb-3">Hubungi kami untuk sampel produk, spesifikasi teknis, atau pengembangan resep custom.</p>
                <a href="contact.php" class="btn btn-primary">Hubungi Sales</a>
                <a href="products.php" class="btn btn-outline-primary ms-2">Lihat Katalog</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section band">
    <div class="text-center mb-4">
        <h2 class="section-title">Produk Unggulan</h2>
        <p class="section-subtitle">Pilihan terbaik dari koleksi CHIBOR yang wajib Anda coba</p>
    </div>
    <div class="product-grid">
        <?php foreach ($featured_products as $p): ?>
            <a class="product-card text-decoration-none" href="products.php?id=<?= $p['id'] ?>">
                <img class="product-img" src="assets/images/<?= htmlspecialchars($p['foto']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>">
                <div class="product-body">
                    <div class="product-title"><?= htmlspecialchars($p['nama']) ?></div>
                    <div class="product-meta"><?= htmlspecialchars($p['nama_kategori'] ?? '') ?> <?= isset($p['nama_subkategori']) ? '• ' . htmlspecialchars($p['nama_subkategori']) : '' ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Sustainability KPIs -->
<section class="section">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
            <h2 class="section-title">Sustainability Nyata</h2>
            <p class="section-subtitle">Kami mengurangi food waste, menggunakan material kemasan recyclable, dan bermitra dengan petani lokal. Setiap inisiatif kami ukur lewat KPI yang transparan.</p>
            <div class="kpis">
                <div class="kpi">
                    <div class="n">-22%</div>
                    <div class="l">Emisi scope 2</div>
                </div>
                <div class="kpi">
                    <div class="n">95%</div>
                    <div class="l">Material recyclable</div>
                </div>
                <div class="kpi">
                    <div class="n">&gt;1.5K</div>
                    <div class="l">Petani bermitra</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="cta">
                <div class="title">Mau kunjungi pabrik?</div>
                <p class="sub mb-3">Jadwalkan tur pabrik kami dan lihat langsung bagaimana standar kualitas diterapkan.</p>
                <a href="contact.php" class="btn btn-primary">Jadwalkan Kunjungan</a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="section band">
    <div class="text-center mb-4">
        <h2 class="section-title">Apa Kata Klien</h2>
        <p class="section-subtitle">Testimoni singkat dari pelanggan B2B dan ritel</p>
    </div>
    <div class="row">
        <?php foreach ($testimonials as $t): ?>
            <div class="col-md-4 mb-3">
                <div class="value h-100">
                    <div class="d">“<?= htmlspecialchars($t['isi']) ?>”</div>
                    <div class="t mt-3">— <?= htmlspecialchars($t['nama']) ?>, <?= htmlspecialchars($t['jabatan'] ?? 'Pelanggan') ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Final CTA -->
<section class="section">
    <div class="cta text-center">
        <div class="title">Ingin Sampel Produk atau Penawaran?</div>
        <p class="sub mb-3">Tim kami siap membantu spesifikasi, MOQ, hingga private label.</p>
        <a href="contact.php" class="btn btn-primary">Hubungi Kami</a>
        <a href="faq.php" class="btn btn-outline-primary ms-2">Lihat FAQ</a>
    </div>
</section>

<?php
include 'includes/footer.php';
?>