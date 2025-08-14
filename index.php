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

<!-- Hero Section -->
<section class="hero-section text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <h1 class="hero-title">
                        <span class="text-white">CRUNCH it ONCE,</span><br>
                        <span class="text-white">CRAVE IT FOREVER!</span>
                    </h1>
                    <p class="hero-subtitle">
                        Nikmati kelezatan snack premium CHIBOR yang renyah dan menggugah selera.
                        Diproduksi dengan standar kualitas tinggi oleh PT. Irgha Reksa Jasa.
                    </p>
                    <div class="hero-cta d-flex flex-wrap">
                        <a href="products.php" class="btn btn-hero btn-hero-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Lihat Produk
                        </a>
                        <a href="https://wa.me/6282320208899" target="_blank" class="btn btn-hero btn-hero-outline">
                            <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="text-center">
                    <img src="assets/images/trolichibor.png" alt="CHIBOR Product" class="img-fluid hover-scale" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0" data-aos="fade-right">
                <img src="assets/images/IRJ-OFFICE.png" alt="Tentang CHIBOR" class="img-fluid rounded shadow-lg">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="section-title">Tentang <span class="gradient-text">CHIBOR</span></h2>
                <p class="section-subtitle">
                    PT. IRGHA REKSA JAYA telah berdedikasi menghadirkan produk snack berkualitas tinggi sejak 12 Juli 2023.
                </p>
                <p class="mb-4">
                    CHIBOR adalah produk unggulan kami yang menghadirkan pengalaman rasa yang tak terlupakan.
                    Dengan proses produksi yang higienis dan menggunakan bahan-bahan pilihan,
                    kami berkomitmen memberikan yang terbaik untuk pelanggan.
                </p>
                <div class="row text-center mb-4">
                    <div class="col-4">
                        <div class="stat-number gradient-text" data-target="100">0</div>
                        <div class="stat-label">% Halal</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-number gradient-text" data-target="2023">0</div>
                        <div class="stat-label">Tahun Berdiri</div>
                    </div>
                    <div class="col-4">
                        <div class="stat-number gradient-text" data-target="1000">0</div>
                        <div class="stat-label">+ Pelanggan</div>
                    </div>
                </div>
                <a href="about.php" class="btn btn-primary btn-lg">
                    Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Produk <span class="gradient-text">Unggulan</span></h2>
            <p class="section-subtitle">Pilihan terbaik dari koleksi CHIBOR yang wajib Anda coba</p>
        </div>

        <div class="row">
            <?php foreach ($featured_products as $index => $product): ?>
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="assets/images/<?= h($product['gambar']) ?>"
                                alt="<?= h($product['nama']) ?>"
                                class="card-img-top product-image">
                            <div class="product-badge"><?= h($product['nama_kategori']) ?></div>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title"><?= h($product['nama']) ?></h5>
                            <p class="product-description"><?= h($product['deskripsi']) ?></p>
                            <?php
                            // Membuat link WhatsApp yang benar
                            $pesan_wa = "Halo, saya ingin memesan produk " . h($product['nama']) . ".";
                            $link_wa_final = "https://wa.me/6282320208899?text=" . urlencode($pesan_wa);
                            ?>
                            <a href="<?= $link_wa_final ?>"
                                target="_blank"
                                class="btn btn-whatsapp w-100">
                                <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="products.php" class="btn btn-primary btn-lg">
                Lihat Semua Produk <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section-padding bg-gradient text-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title text-primary">Mengapa Memilih <span class="text-warning">CHIBOR?</span></h2>
            <p class="section-subtitle text-light">Keunggulan yang membuat CHIBOR menjadi pilihan terbaik</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-certificate fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Sertifikat Halal</h5>
                    <p class="text-dark">Diproduksi dengan standar halal dan higienis terjamin</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-award fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Kualitas Premium</h5>
                    <p class="text-dark">Menggunakan bahan pilihan dengan cita rasa yang unik</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-shipping-fast fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pengiriman Cepat</h5>
                    <p class="text-dark">Layanan pengiriman ke seluruh Indonesia</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-heart fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Kepuasan Pelanggan</h5>
                    <p class="text-dark">Komitmen memberikan yang terbaik untuk setiap pelanggan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<?php if (!empty($testimonials)): ?>
    <section class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Apa Kata <span class="gradient-text">Pelanggan</span></h2>
                <p class="section-subtitle">Testimoni dari pelanggan yang telah merasakan kelezatan CHIBOR</p>
            </div>

            <div class="row">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="testimonial-card">
                            <img src="assets/images/testimonials/<?= h($testimonial['foto']) ?>"
                                alt="<?= h($testimonial['nama_pelanggan']) ?>"
                                class="testimonial-avatar">
                            <div class="testimonial-stars text-warning">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <?php if ($i < $testimonial['rating']): ?>
                                        <i class="fas fa-star"></i> <?php else: ?>
                                        <i class="far fa-star"></i> <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="testimonial-text">"<?= h($testimonial['komentar']) ?>"</p>
                            <h6 class="testimonial-name"><?= h($testimonial['nama_pelanggan']) ?></h6>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="testimoni.php" class="btn btn-primary btn-lg">
                    Lihat Semua Testimoni <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Call to Action -->
<section class="section-padding bg-gradient text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in">
                <h2 class="section-title text-primary mb-4">Siap Merasakan <span class="text-warning">Kelezatan CHIBOR?</span></h2>
                <p class="section-subtitle text-dark mb-5">
                    Jangan tunggu lagi! Pesan sekarang dan rasakan sendiri kelezatan yang bikin nagih.
                    Dapatkan penawaran menarik untuk pembelian dalam jumlah besar.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="https://wa.me/6282320208899?text=Halo, saya ingin memesan produk CHIBOR"
                        target="_blank"
                        class="btn btn-pesan btn-lg px-5">
                        <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                    </a>
                    <a href="products.php" class="btn btn-outline-dark btn-lg px-5">
                        <i class="fas fa-shopping-bag me-2"></i>Lihat Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = '
<script>
    // Custom JavaScript for homepage
    document.addEventListener("DOMContentLoaded", function() {
        // Animate hero text
        const heroTitle = document.querySelector(".hero-title");
        if (heroTitle) {
            heroTitle.style.opacity = "0";
            heroTitle.style.transform = "translateY(30px)";
            setTimeout(() => {
                heroTitle.style.transition = "all 1s ease";
                heroTitle.style.opacity = "1";
                heroTitle.style.transform = "translateY(0)";
            }, 500);
        }
        
        // Product hover effects
        document.querySelectorAll(".product-card").forEach(card => {
            card.addEventListener("mouseenter", function() {
                this.style.transform = "translateY(-10px)";
            });
            
            card.addEventListener("mouseleave", function() {
                this.style.transform = "translateY(0)";
            });
        });
        
        // Counter animation for stats
        const observerOptions = {
            threshold: 0.7,
            triggerOnce: true
        };
        
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute("data-target"));
                    const duration = 2000;
                    const step = target / (duration / 16);
                    let current = 0;
                    
                    const timer = setInterval(() => {
                        current += step;
                        if (target === 2023) {
                            counter.textContent = Math.floor(current);
                        } else {
                            counter.textContent = Math.floor(current);
                        }
                        
                        if (current >= target) {
                            counter.textContent = target;
                            clearInterval(timer);
                        }
                    }, 16);
                    
                    statsObserver.unobserve(counter);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll(".stat-number").forEach(counter => {
            statsObserver.observe(counter);
        });
    });
    
    // Custom order product function
    function orderProduct(productName) {
        const message = `Halo, saya tertarik dengan ${productName}. Bisa tolong berikan informasi lebih lanjut mengenai harga dan cara pemesanan?`;
        const whatsappUrl = `https://wa.me/6282320208899?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, "_blank");
        
        // Track product interest (optional analytics)
        if (typeof gtag !== "undefined") {
            gtag("event", "product_interest", {
                "event_category": "engagement",
                "event_label": productName
            });
        }
    }
</script>
';

include 'includes/footer.php';
?>