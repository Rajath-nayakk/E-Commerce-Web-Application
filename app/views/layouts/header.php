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
    <title><?= isset($title) ? e($title) . ' | NovaTech Gear' : 'NovaTech Gear | Premium Indian Electronics & Hardware Store' ?></title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Design System Stylesheet -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Top Announcement Ribbon -->
<div class="top-ribbon py-1 fw-semibold">
    <div class="container d-flex justify-content-between align-items-center">
        <span><i class="bi bi-shield-check text-warning me-1"></i> 100% Genuine Electronics | Official Brand Warranty</span>
        <span class="d-none d-md-inline"><i class="bi bi-truck text-info me-1"></i> Free Express Delivery Across India on orders over ₹999</span>
        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-lg-inline text-muted">Toll-Free: 1800-889-NOVA</span>
            <?php if (Auth::isAdmin()): ?>
                <a href="/admin" class="text-warning fw-bold text-decoration-none">
                    <i class="bi bi-speedometer2 me-1"></i>Admin Console
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
            <a href="/cart" class="btn btn-outline-dark position-relative btn-cart-mobile">
                <i class="bi bi-bag"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count-badge">
                    <?= $cartCount ?>
                </span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>
        </div>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') ? 'active' : '' ?>" href="/">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/products') ? 'active' : '' ?>" href="/products">
                        <i class="bi bi-grid me-1"></i>Catalog
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-tag me-1"></i>Categories
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="/products?category=<?= e($cat['slug']) ?>">
                                    <span><?= e($cat['name']) ?></span>
                                    <span class="badge bg-light text-muted border rounded-pill small"><?= $cat['product_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item fw-bold text-primary" href="/products">View All Products &rarr;</a></li>
                    </ul>
                </li>
                <?php if (Auth::check()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/orders') ? 'active' : '' ?>" href="/orders">
                            <i class="bi bi-box-seam me-1"></i>My Orders
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Search Form -->
            <form class="d-flex mx-lg-3 my-2 my-lg-0 search-form" action="/products" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Search 4K monitors, ANC audio, keyboards..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
                    <button class="btn btn-primary" type="submit" aria-label="Submit search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- User Auth & Cart Area -->
            <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                <!-- Cart Button -->
                <a href="/cart" class="btn btn-cart-desktop d-none d-lg-inline-flex align-items-center gap-2">
                    <i class="bi bi-bag-fill fs-5 text-primary"></i>
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
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                            <li class="dropdown-header text-uppercase small text-muted">Account Details</li>
                            <li><span class="dropdown-item-text fw-bold text-dark"><?= e($currentUser['email'] ?? '') ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if (Auth::isAdmin()): ?>
                                <li>
                                    <a class="dropdown-item text-primary fw-semibold" href="/admin">
                                        <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="/orders">
                                    <i class="bi bi-box-seam me-2"></i>Order History & Tracking
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
                    <a href="/login" class="btn btn-outline-dark btn-sm px-3 py-2 rounded-pill fw-semibold">Sign In</a>
                    <a href="/register" class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-semibold shadow-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- Secondary Category Bar for Quick Navigation -->
<nav class="category-nav-bar py-1 d-none d-md-block shadow-xs">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex gap-2">
            <a href="/products" class="category-nav-link <?= empty($_GET['category']) && str_starts_with($_SERVER['REQUEST_URI'], '/products') ? 'active' : '' ?>">
                <i class="bi bi-stars text-warning"></i> All Electronics
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="/products?category=<?= e($cat['slug']) ?>" class="category-nav-link <?= (($_GET['category'] ?? '') === $cat['slug']) ? 'active' : '' ?>">
                    <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div>
            <a href="/products?deal=1" class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 text-decoration-none rounded-pill fw-bold">
                <i class="bi bi-fire me-1"></i> Today's Deals
            </a>
        </div>
    </div>
</nav>

<!-- Flash Alerts Partial -->
<?php require __DIR__ . '/alerts.php'; ?>

<!-- Main Content Wrapper -->
<main class="flex-grow-1">
