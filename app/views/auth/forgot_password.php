<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="brand-icon mx-auto mb-2">
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Reset Password</h3>
                    <p class="text-muted small mb-0">Enter your account email to receive a recovery token</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="/forgot-password" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="name@store.com" required autofocus>
                            </div>
                            <div class="form-text mt-2 small text-muted">
                                We will generate a secure recovery token with 1-hour expiration.
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold fs-6">
                                <i class="bi bi-send me-2"></i>Send Reset Link
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="/login" class="small fw-semibold text-decoration-none text-muted">
                            <i class="bi bi-arrow-left me-1"></i>Back to Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

