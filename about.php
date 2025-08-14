<?php
// about.php
session_start();
require_once 'includes/db.php';

$page_title = 'Tentang Kami';
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-white" style="min-height: 60vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="hero-title">Tentang <span class="text-warning">PT. IRGHA REKSA JAYA</span></h1>
                <p class="hero-subtitle">
                    Produsen snack premium CHIBOR yang berkomitmen menghadirkan kelezatan
                    dan kualitas terbaik sejak 2023
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Company Story -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right">
                <h2 class="section-title">Perjalanan <span class="gradient-text">CHIBOR</span></h2>
                <p class="section-subtitle">
                    Dari ide sederhana hingga menjadi brand snack yang digemari
                </p>
                <p class="mb-4">
                    PT. IRGHA REKSA JAYA didirikan pada <strong>September 2024</strong> dan bergerak di bidang produksi makanan ringan (snack). Perusahaan ini fokus mengembangkan snack lokal berkualitas tinggi dengan cita rasa yang menarik dan daya saing global. Produk unggulan pertama PT. Irgha Reksa Jasa adalah keripik singkong bermerek <strong>CHIBOR</strong> (singkatan dari “Chips From Bogor”), yang diharapkan menjadi ikon kelezatan lokal dari Kota Bogor. Perusahaan ini memiliki tujuan strategis untuk membesarkan nama Kota Bogor melalui pengembangan produk snack berkualitas tinggi dan inovatif.
                </p>
                <p class="mb-4">
                    PT. IRGHA REKSA JAYA berkantor pusat di Bogor dan memanfaatkan sumber daya alam Indonesia secara efektif dalam proses produksinya. Dengan visi mendukung perekonomian dan budaya lokal, perusahaan ini berkomitmen menggunakan bahan baku lokal terbaik yang diolah sesuai standar global. Pabrik produksi PT. Irgha Reksa Jasa berlokasi di kawasan industri Kencana, Tanah Sareal, Bogor, yang menjadi pusat operasional dan pengembangan produk. Melalui kerjasama dengan petani dan tenaga kerja lokal, PT. Irgha Reksa Jasa juga berupaya memberdayakan potensi masyarakat setempat dan meningkatkan keterampilan sumber daya manusia di sekitarnya.
                </p>
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="stat-number gradient-text" data-target="1">0</div>
                        <div class="stat-label small">Tahun Pengalaman</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="stat-number gradient-text" data-target="10">0</div>
                        <div class="stat-label small">Varian Produk</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="stat-number gradient-text" data-target="1000">0</div>
                        <div class="stat-label small">+ Pelanggan</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="stat-number gradient-text" data-target="5">0</div>
                        <div class="stat-label small">Kota Jangkauan</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="row">
                    <div class="col-12 mb-4">
                        <img src="assets/images/IRJ-OFFICE.png" alt="Produk CHIBOR" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6 mb-4">
                        <img src="assets/images/produksi.png" alt="Proses Produksi" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6 mb-4">
                        <img src="assets/images/tim.png" alt="Tim CHIBOR" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vision Mission -->
<section class="vision-mission">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Visi & <span class="gradient-text">Misi</span></h2>
            <p class="section-subtitle">Landasan yang menggerakkan setiap langkah kami</p>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="vm-card">
                    <div class="vm-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4 class="fw-bold text-primary mb-3">Visi Kami</h4>
                    <p class="mb-0">
                        Menjadi produsen snack lokal terdepan dengan kualitas dan rasa global,
                        yang memanfaatkan sumber daya alam Indonesia dan memberdayakan potensi
                        pekerja lokal, sehingga Chibor menjadi pilihan utama konsumen dan
                        bersaing di pasar global.

                    </p>
                </div>
            </div>
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="vm-card">
                    <div class="vm-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4 class="fw-bold text-primary mb-3">Misi Kami</h4>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Mengembangkan produk snack singkong Chibor dengan kualitas dan rasa yang konsisten, memenuhi standar global.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Memanfaatkan sumber daya alam Indonesia secara efektif dan berkelanjutan.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Memberdayakan dan meningkatkan potensi pekerja lokal melalui pelatihan dan pengembangan kompetensi.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Meningkatkan kepuasan konsumen melalui inovasi produk dan pelayanan yang prima.
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Membangun jaringan distribusi yang luas dan efektif untuk meningkatkan ketersediaan produk Chibor di pasar domestik dan internasional.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Values -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Nilai-nilai <span class="gradient-text">Perusahaan</span></h2>
            <p class="section-subtitle">Prinsip yang menjadi fondasi dalam setiap aktivitas kami</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="text-center">
                    <div class="vm-icon mx-auto mb-3">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="fw-bold">Kualitas</h5>
                    <p class="text-muted">Berkomitmen memberikan produk dengan standar kualitas tertinggi</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="text-center">
                    <div class="vm-icon mx-auto mb-3">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h5 class="fw-bold">Inovasi</h5>
                    <p class="text-muted">Terus berinovasi dalam mengembangkan produk dan layanan</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="text-center">
                    <div class="vm-icon mx-auto mb-3">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h5 class="fw-bold">Integritas</h5>
                    <p class="text-muted">Menjunjung tinggi kejujuran dan transparansi dalam berbisnis</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="text-center">
                    <div class="vm-icon mx-auto mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="fw-bold">Kepedulian</h5>
                    <p class="text-muted">Peduli terhadap pelanggan, karyawan, dan lingkungan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Company Info -->
<section class="about-stats">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <span class="stat-number" data-target="100">0</span>
                    <div class="stat-label">% Bahan Halal</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <span class="stat-number" data-target="24">0</span>
                    <div class="stat-label">Jam Produksi/Hari</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <span class="stat-number" data-target="50">0</span>
                    <div class="stat-label">+ Karyawan</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <span class="stat-number" data-target="99">0</span>
                    <div class="stat-label">% Kepuasan Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Informasi <span class="gradient-text">Perusahaan</span></h2>
            <p class="section-subtitle">Hubungi kami untuk informasi lebih lanjut</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="row">
                    <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card card-modern h-100 text-center p-4">
                            <div class="vm-icon mx-auto mb-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h5 class="fw-bold">Alamat Kantor</h5>
                            <p class="text-muted mb-0">
                                Jl. Industri No. 123<br>
                                Jakarta Timur, DKI Jakarta<br>
                                Indonesia 13750
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card card-modern h-100 text-center p-4">
                            <div class="vm-icon mx-auto mb-3">
                                <i class="fas fa-phone"></i>
                            </div>
                            <h5 class="fw-bold">Kontak</h5>
                            <p class="text-muted mb-2">
                                <strong>Telepon:</strong><br>
                                <a href="tel:+6282320208899" class="text-decoration-none">0823-2020-8899</a>
                            </p>
                            <p class="text-muted mb-0">
                                <strong>Email:</strong><br>
                                <a href="mailto:info@chibor.com" class="text-decoration-none">info@chibor.com</a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card card-modern h-100 text-center p-4">
                            <div class="vm-icon mx-auto mb-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="fw-bold">Jam Operasional</h5>
                            <p class="text-muted mb-0">
                                <strong>Senin - Jumat:</strong> 08:00 - 17:00<br>
                                <strong>Sabtu:</strong> 08:00 - 15:00<br>
                                <strong>Minggu:</strong> Tutup
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card card-modern h-100 text-center p-4">
                            <div class="vm-icon mx-auto mb-3">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <h5 class="fw-bold">Media Sosial</h5>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="https://www.instagram.com/chibor_official" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="https://www.facebook.com/chibor" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fab fa-facebook"></i>
                                </a>
                                <a href="https://wa.me/6282320208899" target="_blank" class="btn btn-success btn-sm">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <div class="bg-gradient text-dark p-4 rounded">
                <h5 class="fw-bold mb-3">Tertarik Menjadi Reseller atau Distributor?</h5>
                <p class="mb-4">Bergabunglah dengan kami dan dapatkan keuntungan menarik!</p>
                <a href="https://wa.me/6282320208899?text=Halo, saya tertarik untuk menjadi reseller/distributor CHIBOR"
                    target="_blank"
                    class="btn btn-pesan btn-lg">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Kami Sekarang
                </a>
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
                    const duration = 2500;
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
        
        // Add hover effects to cards
        document.querySelectorAll(".vm-card, .card-modern").forEach(card => {
            card.addEventListener("mouseenter", function() {
                this.style.transform = "translateY(-5px)";
                this.style.transition = "all 0.3s ease";
            });
            
            card.addEventListener("mouseleave", function() {
                this.style.transform = "translateY(0)";
            });
        });
    });
</script>
';

include 'includes/footer.php';
?>