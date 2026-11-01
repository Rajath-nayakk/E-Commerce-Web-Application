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
                    <span class="fs-4 fw-bold">NovaTech <span class="text-warning">GEAR</span></span>
                </div>
                <p class="text-muted small">
                    Premium tech hardware, computing accessories, audio gear, and IoT smart devices.
                    Engineered with pure PHP 8.x, PDO, MySQL, and modern security architecture.
                </p>
                <div class="d-flex gap-3 text-muted fs-5 mt-3">
                    <a href="#" class="text-muted text-hover-white"><i class="bi bi-github"></i></a>
                    <a href="#" class="text-muted text-hover-white"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-muted text-hover-white"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Shop</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-muted">
                    <li><a href="/products" class="text-muted text-decoration-none text-hover-white">All Products</a></li>
                    <li><a href="/products?sort=newest" class="text-muted text-decoration-none text-hover-white">New Arrivals</a></li>
                    <li><a href="/products?in_stock=1" class="text-muted text-decoration-none text-hover-white">In Stock Now</a></li>
                    <li><a href="/cart" class="text-muted text-decoration-none text-hover-white">Shopping Cart</a></li>
                </ul>
            </div>

            <!-- Student / Project Architecture (Viva Highlights) -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">System Features</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 text-muted">
                    <li><i class="bi bi-check2 text-success me-1"></i> Framework-free Clean MVC</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> Prepared Statements & CSRF</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> ACID Stock Transactions</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> Order Lifecycle Timeline</li>
                    <li><i class="bi bi-check2 text-success me-1"></i> Session + DB Cart Merge</li>
                </ul>
            </div>

            <!-- Customer & Admin Access -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-warning mb-3">Quick Demo Credentials</h6>
                <div class="bg-dark p-3 rounded small border border-secondary">
                    <div class="mb-2">
                        <strong class="text-white">Admin Account:</strong><br>
                        <span class="text-warning">admin@store.com</span> / <code>Admin@123</code>
                    </div>
                    <div>
                        <strong class="text-white">Customer Account:</strong><br>
                        <span class="text-info">user@store.com</span> / <code>User@123</code>
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted">
            <p class="m-0">&copy; <?= date('Y') ?> NovaTech Gear. College Capstone / Viva Project. Built with PHP 8.x & MySQL.</p>
            <div class="d-flex gap-3 mt-2 mt-md-0">
                <span class="badge bg-secondary">PHP 8.x PDO</span>
                <span class="badge bg-secondary">MySQL / MariaDB</span>
                <span class="badge bg-primary">REST JSON APIs</span>
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

