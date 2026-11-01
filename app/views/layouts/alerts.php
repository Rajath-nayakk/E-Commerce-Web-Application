<?php
use App\Core\Session;

$flashes = Session::getFlashes();
if (!empty($flashes)): ?>
    <div class="alerts-container container my-3">
        <?php foreach ($flashes as $type => $messages): 
            $alertClass = match($type) {
                'success' => 'alert-success',
                'danger', 'error' => 'alert-danger',
                'warning' => 'alert-warning',
                default => 'alert-info'
            };
            $icon = match($type) {
                'success' => 'bi-check-circle-fill',
                'danger', 'error' => 'bi-exclamation-triangle-fill',
                'warning' => 'bi-exclamation-circle-fill',
                default => 'bi-info-circle-fill'
            };
        ?>
            <?php foreach ($messages as $msg): 
                // XSS sanitization: allow only safe link/break/formatting tags, strictly neutralising javascript: and event handlers
                $safeMsg = strip_tags((string) $msg, '<a><br><strong><b><i>');
                if (preg_match('/(javascript:|on\w+=)/i', $safeMsg)) {
                    $safeMsg = htmlspecialchars((string) $msg, ENT_QUOTES, 'UTF-8');
                }
            ?>
                <div class="alert <?= $alertClass ?> alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                    <i class="bi <?= $icon ?> me-2 fs-5"></i>
                    <div class="flex-grow-1"><?= $safeMsg ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

