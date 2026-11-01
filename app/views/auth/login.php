<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="brand-icon mx-auto mb-2">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Welcome Back</h3>
                    <p class="text-muted small mb-0">Sign in to your NovaTech account</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">

                    <?php if (!empty($isDemo)): ?>
                        <!-- Demo 1-Click Fill Buttons (Enabled only when APP_DEMO=true) -->
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-muted text-uppercase">1-Click Demo Login:</span>
                                <span class="badge bg-secondary">Demo Mode Active</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-warning w-50 fw-semibold" onclick="fillLogin('admin@store.com', 'Admin@123')">
                                    <i class="bi bi-person-gear me-1"></i> Admin
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary w-50 fw-semibold" onclick="fillLogin('user@store.com', 'User@123')">
                                    <i class="bi bi-person me-1"></i> Customer
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="/login" method="POST" autocomplete="on">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="name@store.com" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold small text-muted m-0">Password</label>
                                <a href="/forgot-password" class="small text-decoration-none text-primary">Forgot password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password" placeholder="••••••••" required>
                                <button type="button" class="btn btn-light border border-start-0 password-toggle-btn text-muted" data-target="password" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold fs-6">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted m-0">
                            Don't have an account yet? 
                            <a href="/register" class="fw-bold text-primary text-decoration-none">Create an Account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($isDemo)): ?>
<script>
function fillLogin(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>
<?php endif; ?>

