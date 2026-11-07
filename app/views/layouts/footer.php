</main>

<!-- Footer -->
<footer class="main-footer mt-5 pt-5 pb-4 text-white">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Brand Info -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <span class="fs-4 fw-bold">NovaTech <span class="brand-badge">GEAR</span></span>
                </div>
                <p class="text-muted small leading-relaxed">
                    India's premier digital storefront for precision tech hardware, 4K creators displays,
                    spatial studio audio, ergonomic mechanical peripherals, and smart IoT systems.
                </p>
                <!-- Trust & Indian Payment Methods -->
                <div class="mt-3">
                    <span class="text-uppercase small fw-bold text-muted d-block mb-2">Accepted Payment Options</span>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="payment-badge-pill"><i class="bi bi-qr-code text-warning"></i> UPI / QR</span>
                        <span class="payment-badge-pill"><i class="bi bi-credit-card-2-front text-info"></i> RuPay</span>
                        <span class="payment-badge-pill"><i class="bi bi-credit-card text-primary"></i> Visa / MC</span>
                        <span class="payment-badge-pill"><i class="bi bi-bank text-success"></i> NetBanking</span>
                        <span class="payment-badge-pill"><i class="bi bi-cash-stack text-warning"></i> Cash on Delivery</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Shop Departments</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-muted">
                    <li><a href="/products" class="text-muted text-decoration-none text-hover-white">All Products</a></li>
                    <li><a href="/products?category=electronics" class="text-muted text-decoration-none text-hover-white">Laptops & Displays</a></li>
                    <li><a href="/products?category=audio-headphones" class="text-muted text-decoration-none text-hover-white">Audio & Acoustics</a></li>
                    <li><a href="/products?category=wearables-smartwatches" class="text-muted text-decoration-none text-hover-white">Smartwatches & Rings</a></li>
                    <li><a href="/products?category=computer-accessories" class="text-muted text-decoration-none text-hover-white">Workstation Peripherals</a></li>
                    <li><a href="/products?deal=1" class="text-muted text-decoration-none text-hover-white">Flash Deals</a></li>
                </ul>
            </div>

            <!-- Customer Service & Pan-India Delivery -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Customer Assurances</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-muted">
                    <li><i class="bi bi-truck text-info me-2"></i> Free Express Delivery on orders > ₹999</li>
                    <li><i class="bi bi-shield-check text-success me-2"></i> 1-Year Official Brand Warranty</li>
                    <li><i class="bi bi-arrow-repeat text-warning me-2"></i> 7-Day Hassle-Free Replacement</li>
                    <li><i class="bi bi-receipt text-primary me-2"></i> GST Tax Invoice on every order</li>
                    <li><i class="bi bi-geo-alt text-danger me-2"></i> Shipping to 19,000+ Indian PIN Codes</li>
                </ul>
            </div>

            <!-- Quick Demo Credentials for Viva -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Evaluation Demo Accounts</h6>
                <div class="bg-dark p-3 rounded-3 small border border-secondary shadow-sm">
                    <div class="mb-2">
                        <strong class="text-white"><i class="bi bi-person-badge text-warning me-1"></i> Admin Account:</strong><br>
                        <span class="text-warning">admin@store.com</span> / <code>Admin@123</code>
                    </div>
                    <div>
                        <strong class="text-white"><i class="bi bi-person text-info me-1"></i> Customer Account:</strong><br>
                        <span class="text-info">user@store.com</span> / <code>User@123</code>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <p class="m-0">&copy; <?= date('Y') ?> NovaTech Gear India. Built with pure PHP 8.x, PDO, and MySQL.</p>
            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                <span class="badge bg-secondary">PHP 8.x PDO</span>
                <span class="badge bg-secondary">MySQL 8.x / MariaDB</span>
                <span class="badge bg-primary">REST JSON APIs</span>
                <span class="badge bg-success">GST Ready</span>
            </div>
        </div>
    </div>
</footer>

<!-- Toast Container for Live Alerts -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
    <div id="liveToast" class="toast align-items-center text-white border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" id="toastMessage">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <span>Action completed</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom App JavaScript -->
<script src="/assets/js/app.js"></script>
</body>
</html>
