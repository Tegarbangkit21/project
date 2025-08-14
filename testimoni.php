<?php
// testimoni.php
session_start();
require_once 'includes/db.php';

$page_title = 'Testimoni';

// Ambil semua testimoni
$stmt = $pdo->query("SELECT * FROM testimoni ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-white" style="min-height: 60vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="hero-title">Testimoni <span class="text-warning">Pelanggan</span></h1>
                <p class="hero-subtitle">
                    Dengarkan pengalaman nyata dari ribuan pelanggan yang telah merasakan kelezatan CHIBOR
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="text-primary mb-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <div class="stat-number text-primary" data-target="1000">0</div>
                    <div class="stat-label">+ Pelanggan Puas</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star fa-3x"></i>
                    </div>
                    <div class="stat-number text-primary" data-target="5">0</div>
                    <div class="stat-label">Rating Rata-rata</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="text-success mb-3">
                        <i class="fas fa-redo-alt fa-3x"></i>
                    </div>
                    <div class="stat-number text-primary" data-target="95">0</div>
                    <div class="stat-label">% Repeat Order</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white p-4 rounded shadow-sm h-100">
                    <div class="text-info mb-3">
                        <i class="fas fa-share-alt fa-3x"></i>
                    </div>
                    <div class="stat-number text-primary" data-target="80">0</div>
                    <div class="stat-label">% Merekomendasikan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Grid -->
<section class="section-padding">
    <div class="container">
        <?php if (!empty($testimonials)): ?>
            <div class="row">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index % 6) * 100 ?>">
                        <div class="testimonial-card">
                            <div class="d-flex align-items-center mb-3">
                                <img src="assets/images/testimonials/<?= h($testimonial['foto']) ?>"
                                    alt="<?= h($testimonial['nama_pelanggan']) ?>"
                                    class="testimonial-avatar me-3"
                                    style="width: 60px; height: 60px;">
                                <div>
                                    <h6 class="testimonial-name mb-1"><?= h($testimonial['nama_pelanggan']) ?></h6>
                                    <div class="testimonial-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="testimonial-text">"<?= h($testimonial['komentar']) ?>"</p>
                            <div class="text-end">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d M Y', strtotime($testimonial['created_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">Belum Ada Testimoni</h4>
                <p class="text-muted">Jadilah yang pertama memberikan testimoni untuk produk CHIBOR!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add Testimonial Section -->
<section class="section-padding bg-gradient text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center" data-aos="zoom-in">
                <h2 class="section-title text-primary mb-4">
                    Bagikan Pengalaman <span class="text-warning">Anda</span>
                </h2>
                <p class="section-subtitle text-dark mb-5">
                    Sudah merasakan kelezatan CHIBOR? Bagikan pengalaman Anda dan bantu orang lain
                    menemukan produk terbaik kami!
                </p>

                <div class="row mb-5">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-heart text-warning fa-2x me-3"></i>
                            <div class="text-start">
                                <h6 class="mb-1 text-dark">Ceritakan Pengalaman</h6>
                                <small class="text-dark">Bagikan kesan Anda</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-camera text-warning fa-2x me-3"></i>
                            <div class="text-start">
                                <h6 class="mb-1 text-dark">Kirim Foto</h6>
                                <small class="text-dark">Sertakan foto Anda</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-gift text-warning fa-2x me-3"></i>
                            <div class="text-start">
                                <h6 class="mb-1 text-dark">Dapatkan Reward</h6>
                                <small class="text-dark">Kesempatan dapat hadiah</small>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="https://wa.me/6282320208899?text=Halo, saya ingin berbagi testimoni tentang produk CHIBOR"
                    target="_blank"
                    class="btn btn-pesan btn-lg px-5 me-3">
                    <i class="fab fa-whatsapp me-2"></i>Kirim Testimoni
                </a>
                <a href="/contact.php" class="btn btn-outline-dark btn-lg px-5">
                    <i class="fas fa-envelope me-2"></i>Kirim via Email
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Customer Reviews Highlights -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Yang Paling <span class="gradient-text">Disukai</span></h2>
            <p class="section-subtitle">Aspek yang paling dihargai pelanggan dari produk CHIBOR</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-heart fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Rasa yang Unik</h5>
                    <p class="text-muted">"Belum pernah ada snack dengan rasa seperti ini!"</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="bg-success text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-leaf fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Bahan Berkualitas</h5>
                    <p class="text-muted">"Terasa banget kualitas bahan yang digunakan"</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="bg-warning text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Kemasan Menarik</h5>
                    <p class="text-muted">"Packagingnya bagus, cocok buat hadiah"</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="bg-info text-white rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shipping-fast fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Pelayanan Cepat</h5>
                    <p class="text-muted">"Pesan hari ini, besok sudah sampai!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Testimonials (if available) -->
<!-- <section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Video <span class="gradient-text">Testimoni</span></h2>
            <p class="section-subtitle">Saksikan langsung pengalaman pelanggan kami</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in">
                <div class="bg-light p-5 rounded text-center">
                    <i class="fas fa-video fa-4x text-primary mb-4"></i>
                    <h4 class="mb-3">Video Testimoni Segera Hadir</h4>
                    <p class="text-muted mb-4">
                        Kami sedang mengumpulkan video testimoni dari pelanggan setia CHIBOR.
                        Ingin berbagi pengalaman Anda dalam bentuk video?
                    </p>
                    <a href="https://wa.me/6282320208899?text=Halo, saya ingin membuat video testimoni untuk CHIBOR"
                        target="_blank"
                        class="btn btn-primary">
                        <i class="fab fa-whatsapp me-2"></i>Kirim Video Testimoni
                    </a>
                </div>
            </div>
        </div>
    </div>
</section> -->

<!-- Call to Action -->
<section class="section-padding bg-gradient text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in">
                <h2 class="section-title text-primary mb-4">
                    Bergabunglah dengan <span class="text-warning">Ribuan Pelanggan Puas</span>
                </h2>
                <p class="section-subtitle text-dark mb-5">
                    Jangan sampai ketinggalan! Rasakan sendiri mengapa begitu banyak orang jatuh cinta
                    pada kelezatan CHIBOR yang tak terlupakan.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="https://wa.me/6282320208899" target="_blank" class="btn btn-pesan btn-lg px-5">
                        <i class="fab fa-whatsapp me-2"></i>Pesan Sekarang
                    </a>
                    <a href="products.php" class="btn btn-outline-dark btn-lg px-5">
                        <i class="fas fa-shopping-bag me-2"></i>Lihat Produk
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extra_js = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Animate counters when in view
    const observerOptions = {
        threshold: 0.7,
        rootMargin: "0px 0px -100px 0px"
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
                    counter.textContent = Math.floor(current);
                    
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
    
    // Add hover effects to testimonial cards
    document.querySelectorAll(".testimonial-card").forEach(card => {
        card.addEventListener("mouseenter", function() {
            this.style.transform = "translateY(-5px) scale(1.02)";
            this.style.transition = "all 0.3s ease";
        });
        
        card.addEventListener("mouseleave", function() {
            this.style.transform = "translateY(0) scale(1)";
        });
    });
    
    // Add loading animation for testimonial images
    document.querySelectorAll(".testimonial-avatar").forEach(img => {
        img.addEventListener("load", function() {
            this.style.opacity = "1";
        });
        
        img.addEventListener("error", function() {
            this.src = "/assets/images/default-avatar.jpg";
            this.alt = "Avatar default";
        });
        
        // Set initial opacity
        img.style.opacity = "0.3";
        img.style.transition = "opacity 0.3s ease";
    });
    
    // Testimonial card entrance animation
    const testimonialCards = document.querySelectorAll(".testimonial-card");
    testimonialCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(30px)";
        
        setTimeout(() => {
            card.style.transition = "all 0.6s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, index * 150);
    });
    
    // Add click to expand functionality for long testimonials
    document.querySelectorAll(".testimonial-text").forEach(text => {
        const maxLength = 150;
        const originalText = text.textContent;
        
        if (originalText.length > maxLength) {
            const truncated = originalText.substring(0, maxLength) + "...";
            text.innerHTML = `${truncated} <span class="text-primary" style="cursor: pointer; text-decoration: underline;">Baca selengkapnya</span>`;
            
            text.addEventListener("click", function(e) {
                if (e.target.textContent === "Baca selengkapnya") {
                    this.innerHTML = `${originalText} <span class="text-primary" style="cursor: pointer; text-decoration: underline;">Sembunyikan</span>`;
                } else if (e.target.textContent === "Sembunyikan") {
                    this.innerHTML = `${truncated} <span class="text-primary" style="cursor: pointer; text-decoration: underline;">Baca selengkapnya</span>`;
                }
            });
        }
    });
});

// Function to submit testimonial via WhatsApp
function submitTestimonial() {
    const message = "Halo, saya ingin berbagi testimoni tentang produk CHIBOR. Berikut pengalaman saya:\\n\\n[Silakan tulis testimoni Anda di sini]\\n\\nTerima kasih!";
    const whatsappUrl = `https://wa.me/6282320208899?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, "_blank");
    
    // Track testimonial submission intent
    if (typeof gtag !== "undefined") {
        gtag("event", "testimonial_submit_intent", {
            "event_category": "engagement",
            "event_label": "whatsapp"
        });
    }
}

// Function to share testimonial page
function shareTestimonials() {
    if (navigator.share) {
        navigator.share({
            title: "Testimoni Pelanggan CHIBOR",
            text: "Lihat apa kata pelanggan tentang produk CHIBOR yang enak dan bikin nagih!",
            url: window.location.href
        }).catch(err => {
            console.log("Error sharing:", err);
            copyToClipboard(window.location.href);
        });
    } else {
        copyToClipboard(window.location.href);
    }
}

// Auto-refresh testimonials (optional feature)
function refreshTestimonials() {
    // This would typically make an AJAX call to get fresh testimonials
    // For now, just refresh the page
    location.reload();
}

// Add to favorites functionality (if implementing user accounts)
function addToFavorites(testimonialId) {
    // Implementation for adding testimonial to favorites
    showAlert("Testimoni ditambahkan ke favorit!", "success");
}
</script>
';

include 'includes/footer.php';
?>