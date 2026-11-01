<div class="error-page container py-5 text-center">
    <div class="error-card card p-5 mx-auto" style="max-width: 680px;">
        <div class="display-1 text-warning fw-bold mb-3">500</div>
        <h2 class="h3 mb-3">Internal Server Error</h2>
        <p class="text-muted mb-4">
            <?= e($message ?? 'An unexpected error occurred while processing your request.') ?>
        </p>
        <?php if (!empty($trace)): ?>
            <div class="text-start bg-dark text-light p-3 rounded mb-4" style="max-height: 250px; overflow: auto; font-size: 0.82rem;">
                <pre class="m-0"><code><?= e($trace) ?></code></pre>
            </div>
        <?php endif; ?>
        <a href="/" class="btn btn-primary">
            <i class="bi bi-house-door me-2"></i>Return to Home
        </a>
    </div>
</div>

