<?php
use App\Core\Auth;
use App\Models\Cart;

$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= isset($title) ? e($title) . ' | Admin Console' : 'NovaTech Admin Console' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-light">

<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar Navigation -->
        <div class="col-lg-2 admin-sidebar p-3 text-white">
            <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom border-secondary">
                <div class="brand-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <span class="fw-bold d-block">Admin Console</span>
                    <span class="badge bg-warning text-dark small" style="font-size: 0.65rem;">CONTROL PANEL</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="nav flex-column mb-auto">
                <a href="/admin" class="admin-nav-link <?= str_ends_with($_SERVER['REQUEST_URI'] ?? '', '/admin') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/products" class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/products') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
                <a href="/admin/categories" class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/categories') ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="/admin/orders" class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/orders') ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i>
                    <span>Orders</span>
                </a>
                <a href="/admin/users" class="admin-nav-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/users') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Users</span>
                </a>
            </nav>

            <div class="pt-4 mt-4 border-top border-secondary">
                <a href="/" class="btn btn-outline-light btn-sm w-100 mb-2">
                    <i class="bi bi-shop me-1"></i>View Storefront
                </a>
                <form action="/logout" method="POST" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right me-1"></i>Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Admin Content Area -->
        <div class="col-lg-10 p-4">
            <!-- Top Header Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h4 class="fw-bold m-0"><?= e($title ?? 'Dashboard') ?></h4>
                    <span class="text-muted small">NovaTech Gear Management Console</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                        <i class="bi bi-person-check-fill me-1"></i> <?= e($currentUser['name'] ?? 'Admin') ?>
                    </span>
                    <a href="/" class="btn btn-sm btn-outline-secondary rounded-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Live Site
                    </a>
                </div>
            </div>

            <!-- Flash Alerts -->
            <?php require __DIR__ . '/alerts.php'; ?>

            <!-- Page Content -->
            <?= $content ?>
        </div>
    </div>
</div>

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
