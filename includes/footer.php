</main>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="assets/images/logo_chibor.png" alt="CHIBOR" height="40" class="me-2">
                    <h5 class="mb-0 text-primary">CHIBOR</h5>
                </div>
                <p class="mb-3 text-primary">PT. IRGHA REKSA JAYA</p>
                <p class="text-light">CRUNCH it ONCE, CRAVE IT FOREVER!</p>
                <p class="small">Produk snack berkualitas tinggi yang diproduksi dengan standar keamanan pangan terbaik sejak 2023.</p>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="text-primary mb-3">Kontak Kami</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Kencana Green Residance, cluster green park view Blok M9 Kota Bogor Jawa Barat
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:+622512028717" class="text-white text-decoration-none">02512028717</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:+6282320208899" class="text-white text-decoration-none">082320208899</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <a href="mailto:info@chibor.com" class="text-white text-decoration-none">info@irghareksajaya.com</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="text-primary mb-3">Menu</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="/" class="text-white text-decoration-none">Beranda</a></li>
                    <li class="mb-2"><a href="/about.php" class="text-white text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="/products.php" class="text-white text-decoration-none">Produk</a></li>
                    <li class="mb-2"><a href="/testimoni.php" class="text-white text-decoration-none">Testimoni</a></li>
                    <li class="mb-2"><a href="/faq.php" class="text-white text-decoration-none">FAQ</a></li>
                    <li class="mb-2"><a href="/contact.php" class="text-white text-decoration-none">Kontak</a></li>
                </ul>
            </div>

            <div class="col-lg-3 mb-4">
                <h6 class="text-primary mb-3">Ikuti Kami</h6>
                <div class="d-flex gap-2 mb-3">
                    <a href="https://www.instagram.com/chibor.cassava" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/chibor Cassavacrips" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://wa.me/6282320208899" class="btn btn-success btn-sm" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
                <p class="small">Dapatkan update produk terbaru dan promo menarik!</p>
            </div>
        </div>

        <hr class="my-4">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="small mb-0">&copy; <?= date('Y') ?> PT. IRGHA REKSA JAYA. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="small mb-0">Didirikan 12 Juli 2023</p>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="btn btn-primary position-fixed bottom-0 end-0 m-3 rounded-circle" id="backToTop" style="display: none; z-index: 1000;">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="/assets/js/main.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });

    // Back to top button
    window.addEventListener('scroll', function() {
        const backToTop = document.getElementById('backToTop');
        if (window.scrollY > 300) {
            backToTop.style.display = 'block';
        } else {
            backToTop.style.display = 'none';
        }
    });

    document.getElementById('backToTop').addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

<?php if (isset($extra_js)) {
    echo $extra_js;
} ?>
</body>

</html>