<?php
use App\Core\Auth;
use App\Models\Cart;
use App\Models\Category;

$cartCount = Cart::getCount();
$categories = Category::allWithCount();
$currentUser = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= isset($title) ? e($title) . ' | NovaTech Gear' : 'NovaTech Gear | Modern Tech & Hardware Store' ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.php.css" rel="stylesheet" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css';">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Design System Stylesheet -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Top Announcement Ribbon -->
<div class="top-ribbon py-1 text-center text-white small fw-semibold">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-check me-1"></i> Official Warranty & 30-Day Money Back Guarantee</span>
        <span class="d-none d-md-inline"><i class="bi bi-truck me-1"></i> Free Shipping on orders over $100</span>
        <div class="d-flex gap-3">
            <?php if (Auth::isAdmin()): ?>
                <a href="/admin" class="text-warning fw-bold text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Admin Panel
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Sticky Header -->
<header class="navbar navbar-expand-lg sticky-top main-navbar py-2 shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <div class="brand-icon">
                <i class="bi bi-cpu-fill"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">NovaTech</span>
                <span class="brand-badge">GEAR</span>
            </div>
        </a>

        <!-- Mobile Toggler & Mobile Cart -->
        <div class="d-flex align-items-center gap-2 d-lg-none">
            <a href="/cart" class="btn btn-outline-light position-relative btn-cart-mobile">
                <i class="bi bi-bag"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge">
                    <?= $cartCount ?>
                </span>
            </a>
            <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
        </div>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="bi bi-house me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/products"><i class="bi bi-grid me-1"></i>Catalog</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-tag me-1"></i>Categories
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow">
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center" href="/products?category=<?= e($cat['slug']) ?>">
                                    <span><?= e($cat['name']) ?></span>
                                    <span class="badge bg-secondary rounded-pill small"><?= $cat['product_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-semibold text-primary" href="/products">View All Products &rarr;</a></li>
                    </ul>
                </li>
                <?php if (Auth::check()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/orders"><i class="bi bi-box-seam me-1"></i>My Orders</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Search Form -->
            <form class="d-flex mx-lg-3 my-2 my-lg-0 search-form" action="/products" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Search monitors, audio, watches..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- User Auth & Cart Area -->
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                <!-- Cart Button -->
                <a href="/cart" class="btn btn-outline-light position-relative d-none d-lg-inline-flex align-items-center gap-2 px-3 py-2 btn-cart-desktop">
                    <i class="bi bi-bag-fill fs-5"></i>
                    <span>Cart</span>
                    <span class="badge rounded-pill bg-danger cart-count-badge">
                        <?= $cartCount ?>
                    </span>
                </a>

                <?php if (Auth::check()): ?>
                    <!-- Account Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-user-menu dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="d-none d-xl-inline fw-semibold"><?= e($currentUser['name'] ?? 'User') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                            <li class="dropdown-header text-uppercase small text-muted">Signed in as</li>
                            <li><span class="dropdown-item-text fw-bold text-white"><?= e($currentUser['email'] ?? '') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (Auth::isAdmin()): ?>
                                <li>
                                    <a class="dropdown-item text-warning" href="/admin">
                                        <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="/orders">
                                    <i class="bi bi-box-seam me-2"></i>Order History
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/logout" method="POST" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Guest Sign In / Register Buttons -->
                    <a href="/login" class="btn btn-outline-light btn-sm px-3 py-2">Sign In</a>
                    <a href="/register" class="btn btn-primary btn-sm px-3 py-2">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Flash Alerts Partial -->
<?php require __DIR__ . '/alerts.php'; ?>

<!-- Main Content Wrapper -->
<main class="flex-grow-1">
