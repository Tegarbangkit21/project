<?php
// faq.php
session_start();
require_once 'includes/db.php';

$page_title = 'FAQ';

// Ambil semua FAQ
$stmt = $pdo->query("SELECT * FROM faq ORDER BY id ASC");
$faqs = $stmt->fetchAll();

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
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text"
                        class="form-control"
                        id="faqSearch"
                        placeholder="Cari pertanyaan..."
                        autocomplete="off">
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Ketik kata kunci untuk menemukan jawaban yang Anda cari</small>
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

        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card card-modern text-center h-100 category-card" data-category="pemesanan">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Pemesanan</h5>
                        <p class="text-muted">Cara pesan, pembayaran, minimum order</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card card-modern text-center h-100 category-card" data-category="produk">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-box fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold">Produk</h5>
                        <p class="text-muted">Informasi produk, halal, expired</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card card-modern text-center h-100 category-card" data-category="pengiriman">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-truck fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold">Pengiriman</h5>
                        <p class="text-muted">Ongkir, waktu kirim, area jangkauan</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card card-modern text-center h-100 category-card" data-category="custom">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-paint-brush fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold">Custom</h5>
                        <p class="text-muted">Produk custom, packaging, branding</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion -->
<section class="section-padding bg-light">
    <div class="container">
        <?php if (!empty($faqs)): ?>
            <div class="faq-accordion" data-aos="fade-up">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="faq-item" data-faq-category="<?= $index < 3 ? 'pemesanan' : ($index < 6 ? 'produk' : ($index < 9 ? 'pengiriman' : 'custom')) ?>">
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
            </div>
        <?php else: ?>
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-question-circle fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">FAQ Belum Tersedia</h4>
                <p class="text-muted">Kami sedang menyiapkan daftar pertanyaan yang sering ditanyakan.</p>
            </div>
        <?php endif; ?>

        <!-- No Results State -->
        <div id="noResults" class="text-center py-5" style="display: none;" data-aos="fade-up">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h4 class="text-muted mb-3">Tidak Ditemukan</h4>
            <p class="text-muted mb-4">Maaf, tidak ada FAQ yang sesuai dengan pencarian Anda.</p>
            <button class="btn btn-primary" onclick="resetSearch()">
                <i class="fas fa-refresh me-2"></i>Reset Pencarian
            </button>
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
    const faqItems = document.querySelectorAll(".faq-item");
    const categoryCards = document.querySelectorAll(".category-card");
    const noResults = document.getElementById("noResults");
    
    // FAQ Search functionality
    searchInput.addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        searchFAQs(searchTerm);
    });
    
    // FAQ Accordion functionality
    document.querySelectorAll(".faq-header").forEach(header => {
        header.addEventListener("click", function() {
            const isActive = this.classList.contains("active");
            
            // Close all FAQ items
            document.querySelectorAll(".faq-header").forEach(h => {
                h.classList.remove("active");
                h.nextElementSibling.classList.remove("active");
            });
            
            // Open clicked item if it wasn\'t active
            if (!isActive) {
                this.classList.add("active");
                this.nextElementSibling.classList.add("active");
            }
        });
    });
    
    // Category filter functionality
    categoryCards.forEach(card => {
        card.addEventListener("click", function() {
            const category = this.getAttribute("data-category");
            filterByCategory(category);
            
            // Update active state
            categoryCards.forEach(c => c.classList.remove("border-primary"));
            this.classList.add("border-primary");
        });
    });
    
    function searchFAQs(searchTerm) {
        let visibleCount = 0;
        
        faqItems.forEach(item => {
            const question = item.querySelector(".faq-header span").textContent.toLowerCase();
            const answer = item.querySelector(".faq-content").textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = "block";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });
        
        toggleNoResults(visibleCount === 0 && searchTerm.length > 0);
    }
    
    function filterByCategory(category) {
        let visibleCount = 0;
        
        faqItems.forEach(item => {
            const itemCategory = item.getAttribute("data-faq-category");
            
            if (itemCategory === category) {
                item.style.display = "block";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });
        
        // Clear search when filtering by category
        searchInput.value = "";
        toggleNoResults(false);
    }
    
    function toggleNoResults(show) {
        if (show) {
            noResults.style.display = "block";
            document.querySelector(".faq-accordion").style.display = "none";
        } else {
            noResults.style.display = "none";
            document.querySelector(".faq-accordion").style.display = "block";
        }
    }
    
    // Add hover effects to category cards
    categoryCards.forEach(card => {
        card.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-5px)";
            this.style.transition = "all 0.3s ease";
        });
        
        card.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0)";
        });
    });
    
    // Add smooth scroll to FAQ when clicking category
    categoryCards.forEach(card => {
        card.addEventListener("click", function() {
            document.querySelector(".faq-accordion").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        });
    });
    
    // Add keyboard navigation for FAQ
    document.addEventListener("keydown", function(e) {
        if (e.key === "Enter" && document.activeElement.classList.contains("faq-header")) {
            document.activeElement.click();
        }
    });
    
    // Auto-expand first FAQ item
    const firstFaqHeader = document.querySelector(".faq-header");
    if (firstFaqHeader) {
        setTimeout(() => {
            firstFaqHeader.click();
        }, 1000);
    }
});

function resetSearch() {
    document.getElementById("faqSearch").value = "";
    document.querySelectorAll(".faq-item").forEach(item => {
        item.style.display = "block";
    });
    document.querySelector(".faq-accordion").style.display = "block";
    document.getElementById("noResults").style.display = "none";
    
    // Remove category filter
    document.querySelectorAll(".category-card").forEach(card => {
        card.classList.remove("border-primary");
    });
}

function contactSupport(method) {
    let url = "";
    const message = "Halo, saya punya pertanyaan yang belum terjawab di FAQ CHIBOR.";
    
    switch(method) {
        case "whatsapp":
            url = `https://wa.me/6282320208899?text=${encodeURIComponent(message)}`;
            break;
        case "email":
            url = `mailto:info@chibor.com?subject=Pertanyaan tentang CHIBOR&body=${encodeURIComponent(message)}`;
            break;
        case "phone":
            url = "tel:+6282320208899";
            break;
    }
    
    if (url) {
        window.open(url, method === "email" ? "_self" : "_blank");
    }
    
    // Track contact attempt
    if (typeof gtag !== "undefined") {
        gtag("event", "contact_support", {
            "event_category": "support",
            "event_label": method
        });
    }
}
</script>
';

include 'includes/footer.php';
?>