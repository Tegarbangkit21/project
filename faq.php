<?php
// faq.php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
$page_title = 'FAQ';

// Ambil semua FAQ dari database, diurutkan berdasarkan kategori
$faqs = $pdo->query("SELECT * FROM faq ORDER BY kategori_faq, id ASC")->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-white" style="min-height: 60vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="hero-title">Frequently Asked <span class="text-warning">Questions</span></h1>
                <p class="hero-subtitle">
                    Temukan jawaban atas pertanyaan yang sering diajukan seputar produk CHIBOR
                </p>
            </div>
        </div>
    </div>
</section>
<!-- Search FAQ -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="faqSearch" placeholder="Cari pertanyaan...">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- FAQ Categories -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Kategori <span class="gradient-text">Pertanyaan</span></h2>
            <p class="section-subtitle">Pilih kategori sesuai dengan yang ingin Anda ketahui</p>
        </div>
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="50">
                <div class="card card-modern text-center h-100 category-card active" data-category="all">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-th-large fa-3x text-secondary"></i></div>
                        <h5 class="fw-bold">Semua</h5>
                        <p class="text-muted">Lihat semua pertanyaan</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-modern text-center h-100 category-card" data-category="pemesanan">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-shopping-cart fa-3x text-primary"></i></div>
                        <h5 class="fw-bold">Pemesanan</h5>
                        <p class="text-muted">Cara pesan, pembayaran</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-modern text-center h-100 category-card" data-category="produk">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-box fa-3x text-success"></i></div>
                        <h5 class="fw-bold">Produk</h5>
                        <p class="text-muted">Info produk, halal, expired</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card card-modern text-center h-100 category-card" data-category="pengiriman">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-truck fa-3x text-warning"></i></div>
                        <h5 class="fw-bold">Pengiriman</h5>
                        <p class="text-muted">Ongkir, waktu, jangkauan</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card card-modern text-center h-100 category-card" data-category="custom">
                    <div class="card-body">
                        <div class="mb-3"><i class="fas fa-paint-brush fa-3x text-info"></i></div>
                        <h5 class="fw-bold">Custom</h5>
                        <p class="text-muted">Packaging, branding</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-light pt-0">
    <div class="container">
        <div class="faq-accordion" id="faqContainer" data-aos="fade-up">
            <?php if (!empty($faqs)): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item" data-faq-category="<?= h($faq['kategori_faq']) ?>">
                        <button class="faq-header" type="button">
                            <span><?= h($faq['pertanyaan']) ?></span>
                            <i class="fas fa-chevron-down faq-icon"></i>
                        </button>
                        <div class="faq-body">
                            <div class="faq-content">
                                <?= nl2br(h($faq['jawaban'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada FAQ yang ditambahkan.</p>
                </div>
            <?php endif; ?>
        </div>
        <div id="noResults" class="text-center py-5" style="display: none;">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h4 class="text-muted mb-3">Tidak Ditemukan</h4>
            <p class="text-muted">Maaf, tidak ada FAQ yang sesuai dengan filter atau pencarian Anda.</p>
        </div>
    </div>
</section>
<!-- Contact Support -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Masih Ada <span class="gradient-text">Pertanyaan?</span></h2>
            <p class="section-subtitle">Tim customer service kami siap membantu Anda 24/7</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-modern text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fab fa-whatsapp fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold">WhatsApp</h5>
                        <p class="text-muted mb-4">Respon cepat dalam hitungan menit</p>
                        <a href="https://wa.me/6282320208899?text=Halo, saya punya pertanyaan tentang CHIBOR"
                            target="_blank"
                            class="btn btn-success w-100">
                            <i class="fab fa-whatsapp me-2"></i>Chat Sekarang
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-modern text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-envelope fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Email</h5>
                        <p class="text-muted mb-4">Untuk pertanyaan detail dan formal</p>
                        <a href="mailto:info@irghareksajaya.com?subject=Pertanyaan tentang CHIBOR" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i>Kirim Email
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card card-modern text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-phone fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold">Telepon</h5>
                        <p class="text-muted mb-4">Hubungi langsung customer service</p>
                        <a href="tel:+622512028717" class="btn btn-info w-100">
                            <i class="fas fa-phone me-2"></i>02512028717
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Tips -->
<section class="section-padding bg-gradient text-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title text-primary">Tips <span class="text-warning">Berguna</span></h2>
            <p class="section-subtitle text-light">Informasi penting yang perlu Anda ketahui</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Jam Operasional</h5>
                    <p class="text-dark">Senin-Jumat: 08:00-17:00<br>Sabtu: 08:00-15:00</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-shipping-fast fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pengiriman</h5>
                    <p class="text-dark">Same day delivery untuk area Jakarta<br>1-3 hari untuk luar Jakarta</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-credit-card fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pembayaran</h5>
                    <p class="text-dark">Transfer bank, e-wallet, dan COD untuk area tertentu</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-undo fa-3x text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Garansi</h5>
                    <p class="text-dark">100% uang kembali jika produk tidak sesuai atau rusak</p>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
$extra_js = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("faqSearch");
    const categoryCards = document.querySelectorAll(".category-card");
    const faqItems = document.querySelectorAll(".faq-item");
    const noResults = document.getElementById("noResults");
    const faqContainer = document.getElementById("faqContainer");

    function filterAndSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const activeCard = document.querySelector(".category-card.active");
        const activeCategory = activeCard ? activeCard.getAttribute("data-category") : "all";
        let visibleCount = 0;

        faqItems.forEach(item => {
            const question = item.querySelector(".faq-header span").textContent.toLowerCase();
            const answer = item.querySelector(".faq-content").textContent.toLowerCase();
            const itemCategory = item.getAttribute("data-faq-category");

            const matchesCategory = (activeCategory === "all" || itemCategory === activeCategory);
            const matchesSearch = (searchTerm.length === 0 || question.includes(searchTerm) || answer.includes(searchTerm));

            if (matchesCategory && matchesSearch) {
                item.style.display = "block";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });

        const hasContent = visibleCount > 0;
        noResults.style.display = hasContent ? "none" : "block";
        faqContainer.style.display = hasContent ? "block" : "none";
    }

    // Event listener untuk input pencarian
    searchInput.addEventListener("input", filterAndSearch);
    
    // Event listener untuk kartu kategori
    categoryCards.forEach(card => {
        card.addEventListener("click", function() {
            categoryCards.forEach(c => c.classList.remove("active", "border-primary"));
            this.classList.add("active", "border-primary");
            filterAndSearch();
            faqContainer.scrollIntoView({ behavior: "smooth", block: "start" });
        });
    });

    // Fungsi untuk accordion
    document.querySelectorAll(".faq-header").forEach(header => {
        header.addEventListener("click", function() {
            const body = this.nextElementSibling;
            const isActive = this.classList.contains("active");

            // Tutup semua item lain
            document.querySelectorAll(".faq-header").forEach(h => {
                if (h !== this) {
                    h.classList.remove("active");
                    h.nextElementSibling.style.maxHeight = null;
                }
            });

            // Buka atau tutup item yang diklik
            if (isActive) {
                this.classList.remove("active");
                body.style.maxHeight = null;
            } else {
                this.classList.add("active");
                body.style.maxHeight = body.scrollHeight + "px";
            }
        });
    });

    // Set "Semua" sebagai aktif di awal
    document.querySelector(".category-card[data-category=\'all\']").classList.add("active", "border-primary");
});
</script>
';

// Anda perlu beberapa CSS tambahan untuk accordion di style.css agar animasi berfungsi
/*
.faq-header { transition: background-color 0.3s ease; }
.faq-header.active { background-color: #f8f9fa; }
.faq-header.active .faq-icon { transform: rotate(180deg); transition: transform 0.3s ease; }
.faq-icon { transition: transform 0.3s ease; }
.faq-body { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; }
*/

include 'includes/footer.php';
?>