<?php
// products.php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
$page_title = 'Produk';

/* 1) Baca kategori dari URL + whitelist */
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$allowed = ['Retail', 'Specialties', 'Private Label'];
if (!in_array($selected_category, $allowed, true)) {
    // kalau kategori tidak valid, default ke kategori pertama
    $selected_category = $allowed[0];
}

/* 2) Ambil semua kategori */
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY id ASC");
$categories = $stmt->fetchAll();

/* 3) Ambil produk TERFILTER */
$sql = "SELECT p.*, k.nama_kategori, s.nama_subkategori
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        LEFT JOIN subkategori s ON p.id_subkategori = s.id
        WHERE k.nama_kategori = :cat
        ORDER BY k.nama_kategori, p.nama";

$stmt = $pdo->prepare($sql);
$stmt->execute([':cat' => $selected_category]);
$products = $stmt->fetchAll();

/* 4) Ambil testimoni */
$stmt_testimonials = $pdo->query("SELECT * FROM testimoni ORDER BY id DESC LIMIT 3");
$testimonials = $stmt_testimonials->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-white" style="min-height: 60vh;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                <h1 class="hero-title">Katalog <span class="text-warning">Produk CHIBOR</span></h1>
                <p class="hero-subtitle">
                    Temukan berbagai varian CHIBOR yang menggugah selera dan bikin nagih!
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Product Search & Filter -->
<!-- Category Filter -->
<!-- Category Tabs (tanpa "Semua Produk") -->
<section class="section-padding bg-light">
    <div class="container">
        <!-- Search Bar (biarkan seperti punya kamu) -->
        <div class="row mb-4">
            <div class="col-lg-6 mx-auto" data-aos="fade-up">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-primary text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="productSearch"
                        placeholder="Cari produk CHIBOR..." autocomplete="off">
                </div>
            </div>
        </div>

        <!-- Kategori sebagai link -->
        <div class="category-tabs text-center" data-aos="fade-up" data-aos-delay="100">
            <?php
            // daftar kategori tampil
            $displayCats = ['Retail' => 'shopping-basket', 'Specialties' => 'star', 'Private Label' => 'star'];
            foreach ($displayCats as $catName => $icon) :
                $active = ($selected_category === $catName) ? 'active' : '';
            ?>
                <a class="pill <?= $active ?>" href="products.php?category=<?= urlencode($catName) ?>">
                    <i class="fas fa-<?= $icon ?> me-2"></i><?= htmlspecialchars($catName) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row" id="productsGrid">
            <?php foreach ($products as $index => $product): ?>
                <?php if ($product['nama_kategori'] !== 'Custom'): ?>

                    <?php
                    // --- SOLUSI LENGKAP UNTUK GAMBAR ---
                    $gambar_nama = htmlspecialchars($product['gambar']);
                    // Path relatif, tanpa garis miring di awal agar berfungsi di localhost
                    $path_gambar_produk = 'assets/images/' . $gambar_nama;

                    // Set gambar final ke placeholder sebagai default
                    $path_gambar_final = 'assets/images/gambar_rusak.png';

                    // Cek jika nama gambar tidak kosong DAN filenya benar-benar ada di folder
                    if (!empty($gambar_nama) && file_exists($path_gambar_produk)) {
                        // Jika ada, gunakan gambar produk asli
                        $path_gambar_final = $path_gambar_produk;
                    }
                    ?>

                    <div class="col-lg-4 col-md-6 mb-4 product-item" data-category="<?= htmlspecialchars($product['nama_kategori']) ?>">
                        <div class="card product-card h-100">
                            <div class="position-relative overflow-hidden">

                                <img src="<?= $path_gambar_final ?>"
                                    alt="<?= htmlspecialchars($product['nama']) ?>"
                                    class="card-img-top product-image"
                                    loading="lazy">

                                <div class="product-badge">
                                    <?= htmlspecialchars($product['nama_kategori']) ?>
                                    <?php if ($product['nama_subkategori']): ?>
                                        - <?= htmlspecialchars($product['nama_subkategori']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="product-overlay">
                                    <button class="btn btn-light btn-sm"
                                        onclick="viewProduct('<?= htmlspecialchars($product['nama'], ENT_QUOTES) ?>', '<?= htmlspecialchars($product['deskripsi'], ENT_QUOTES) ?>', '<?= $path_gambar_final ?>')">
                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                    </button>
                                </div>
                            </div>
                            <div class="product-info">
                                <h5 class="product-title"><?= htmlspecialchars($product['nama']) ?></h5>
                                <p class="product-description"><?= htmlspecialchars($product['deskripsi']) ?></p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($product['nama_kategori']) ?>
                                    </span>
                                    <div class="product-actions">
                                        <button class="btn btn-outline-primary btn-sm"
                                            onclick="shareProduct('<?= htmlspecialchars($product['nama'], ENT_QUOTES) ?>', window.location.href)"
                                            title="Bagikan produk">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>

                                <a href="<?= htmlspecialchars($product['link_wa']) ?>"
                                    target="_blank"
                                    class="btn btn-whatsapp w-100"
                                    $link_wa_final="orderProduct('<?= htmlspecialchars($product['nama'], ENT_QUOTES) ?>')">
                                    <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div id="emptyState" class="text-center py-5" style="display: none;">
            <div data-aos="zoom-in">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">Produk Tidak Ditemukan</h4>
                <p class="text-muted mb-4">Maaf, tidak ada produk yang sesuai dengan pencarian Anda.</p>
                <button class="btn btn-primary" onclick="resetSearch()">
                    <i class="fas fa-refresh me-2"></i>Reset Pencarian
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Custom Product Section -->
<section class="section-padding bg-gradient text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in">
                <div class="mb-4">
                    <i class="fas fa-paint-brush fa-4x text-warning mb-4"></i>
                </div>
                <h2 class="section-title text-primary mb-4">
                    Butuh Produk <span class="text-warning">Custom?</span>
                </h2>
                <p class="section-subtitle text-dark mb-5">
                    Kami menyediakan layanan custom untuk rasa, packaging, dan branding sesuai kebutuhan Anda.
                    Cocok untuk acara khusus, corporate gift, atau bisnis reseller.
                </p>

                <div class="row mb-5">
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-palette text-warning me-2"></i>
                            <span class="text-dark">Custom Rasa</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-box text-warning me-2"></i>
                            <span class="text-dark">Custom Packaging</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="fas fa-tags text-warning me-2"></i>
                            <span class="text-dark">Custom Branding </span>
                        </div>
                    </div>
                </div>

                <a href="https://wa.me/6282320208899?text=Halo, saya tertarik dengan layanan custom CHIBOR. Bisa tolong berikan informasi lebih lanjut?"
                    target="_blank"
                    class="btn btn-pesan btn-lg px-5">
                    <i class="fab fa-whatsapp me-2"></i>Konsultasi Custom Product
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="productModalImage" src="" alt="" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6">
                        <h4 id="productModalName" class="fw-bold mb-3"></h4>
                        <p id="productModalDescription" class="text-muted mb-4"></p>

                        <div class="mb-4">
                            <h6 class="fw-bold">Keunggulan Produk:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Bahan berkualitas premium</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Sertifikat halal terjamin</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Kemasan higienis dan menarik</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Rasa yang unik dan menggugah selera</li>
                            </ul>
                        </div>

                        <a id="productModalWhatsApp"
                            href="#"
                            target="_blank"
                            class="btn btn-whatsapp btn-lg w-100">
                            <i class="fab fa-whatsapp me-2"></i>Pesan via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonial Section -->
<?php if (!empty($testimonials)): ?>
    <section class="section-padding bg-light">
        <div class="container text-center">
            <div data-aos="fade-up">
                <h3 class="fw-bold mb-3">Apa Kata Pelanggan?</h3>
                <p class="text-muted mb-5">Pelanggan kami menyukai setiap gigitannya!</p>
                <div class="row">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="col-md-4 mb-3">
                            <div class="bg-white p-4 rounded shadow-sm h-100">
                                <div class="text-warning mb-2">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="<?= $i < $testimonial['rating'] ? 'fas fa-star' : 'far fa-star' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="small fst-italic">"<?= h($testimonial['komentar']) ?>"</p>
                                <small class="text-muted">- <?= h($testimonial['nama_pelanggan']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="testimoni.php" class="btn btn-primary mt-4">
                    Lihat Semua Testimoni <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
// ... (kode HTML lainnya di atas) ...

$extra_js = '
<script>
// ganti isi script kategori lama jadi hanya search:
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("productSearch");
  const productItems = document.querySelectorAll(".product-item");
  const emptyState = document.getElementById("emptyState");

  function runSearch(term){
    let visible = 0;
    const q = term.toLowerCase().trim();
    productItems.forEach(item => {
      const name = item.querySelector(".product-title")?.textContent.toLowerCase() || "";
      const show = !q || name.includes(q);
      item.style.display = show ? "" : "none";
      if (show) visible++;
    });
    emptyState.style.display = (visible === 0) ? "block" : "none";
  }

  searchInput.addEventListener("input", () => runSearch(searchInput.value));
  runSearch("");
});


    // --- FUNGSI UNTUK PENCARIAN (DIPERBAIKI) ---
    function filterBySearch(searchTerm) {
        let visibleCount = 0;
        const activeCategory = document.querySelector(".filter-btn.active").getAttribute("data-filter");

        productItems.forEach(item => {
            // Hanya proses item yang tidak disembunyikan oleh filter kategori
            if (!item.classList.contains("hidden") || activeCategory === "all") {
                const productName = item.querySelector(".product-title").textContent.toLowerCase();
                const shouldShow = productName.includes(searchTerm);
                
                // Toggle berdasarkan hasil pencarian, tapi hormati filter kategori
                const itemCategory = item.getAttribute("data-category");
                if (activeCategory === "all" || itemCategory === activeCategory) {
                    item.classList.toggle("hidden", !shouldShow);
                    if(shouldShow) visibleCount++;
                }
            }
        });
        emptyState.style.display = visibleCount === 0 ? "block" : "none";
    }

    // Event listener untuk tombol filter
    filterButtons.forEach(button => {
        button.addEventListener("click", function() {
            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
            
            const category = this.getAttribute("data-filter");
            filterByCategory(category);

            // Setelah filter, pastikan pencarian juga diterapkan
            const searchTerm = searchInput.value.toLowerCase();
            if (searchTerm) {
                filterBySearch(searchTerm);
            }
        });
    });

    // Event listener untuk input pencarian
    searchInput.addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        // Saat mengetik, jalankan filter kategori dulu, baru pencarian
        const activeCategory = document.querySelector(".filter-btn.active").getAttribute("data-filter");
        filterByCategory(activeCategory);
        filterBySearch(searchTerm);
    });

});

// Product modal functions
function viewProduct(name, description, image) {
    document.getElementById("productModalName").textContent = name;
    document.getElementById("productModalDescription").textContent = description;
    document.getElementById("productModalImage").src = image;
    document.getElementById("productModalImage").alt = name;
    
    const whatsappLink = `https://wa.me/6282320208899?text=${encodeURIComponent("Halo, saya tertarik dengan " + name + ". Bisa tolong berikan informasi lebih lanjut?")}`;
    document.getElementById("productModalWhatsApp").href = whatsappLink;
    
    new bootstrap.Modal(document.getElementById("productModal")).show();
}

function orderProduct(productName) {
    const message = `Halo, saya ingin memesan ${productName}. Bisa tolong berikan informasi mengenai harga dan cara pemesanan?`;
    const whatsappUrl = `https://wa.me/6282320208899?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, "_blank");
    
    // Analytics tracking (optional)
    if (typeof gtag !== "undefined") {
        gtag("event", "product_order_intent", {
            "event_category": "ecommerce",
            "event_label": productName
        });
    }
}

function shareProduct(productName, productUrl) {
    if (navigator.share) {
        navigator.share({
            title: productName + " - CHIBOR",
            text: `Lihat produk ${productName} dari CHIBOR yang enak dan bikin nagih!`,
            url: productUrl
        }).catch(err => {
            console.log("Error sharing:", err);
            copyToClipboard(productUrl);
        });
    } else {
        copyToClipboard(productUrl);
    }
}

function resetSearch() {
    document.getElementById("productSearch").value = "";
    document.querySelector(".filter-btn[data-filter=\"all\"]").click();
}

</script>
';

include 'includes/footer.php';
?>