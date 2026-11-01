<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="brand-icon mx-auto mb-2">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Create Account</h3>
                    <p class="text-muted small mb-0">Join NovaTech Gear to track orders and save your cart</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="/register" method="POST" autocomplete="on">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold small text-muted">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control border-start-0" id="name" name="name" value="<?= e(old('name')) ?>" placeholder="Jane Doe" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-muted">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control border-start-0" id="email" name="email" value="<?= e(old('email')) ?>" placeholder="jane@example.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-muted">Password (min 6 characters)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password" placeholder="••••••••" required>
                                <button type="button" class="btn btn-light border border-start-0 password-toggle-btn text-muted" data-target="password" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label fw-semibold small text-muted">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shield-check"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
                                <button type="button" class="btn btn-light border border-start-0 password-toggle-btn text-muted" data-target="password_confirm" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold fs-6">
                                <i class="bi bi-check-circle me-2"></i>Create Free Account
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted m-0">
                            Already registered? 
                            <a href="/login" class="fw-bold text-primary text-decoration-none">Sign In Here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

