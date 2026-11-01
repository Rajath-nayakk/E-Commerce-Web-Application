<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center border-0">
                    <div class="brand-icon mx-auto mb-2">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Set New Password</h3>
                    <p class="text-muted small mb-0">Resetting password for <?= e($email ?? 'your account') ?></p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <form action="/reset-password" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= e($token) ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-muted">New Password (min 6 characters)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="password" name="password" placeholder="••••••••" required autofocus>
                                <button type="button" class="btn btn-light border border-start-0 password-toggle-btn text-muted" data-target="password" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label fw-semibold small text-muted">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control border-start-0 border-end-0" id="password_confirm" name="password_confirm" placeholder="••••••••" required>
                                <button type="button" class="btn btn-light border border-start-0 password-toggle-btn text-muted" data-target="password_confirm" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold fs-6">
                                <i class="bi bi-check-lg me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
