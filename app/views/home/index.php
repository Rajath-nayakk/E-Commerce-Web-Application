<!-- Hero Showcase Banner -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-pill">
                    <i class="bi bi-stars text-warning"></i>
                    <span>Next-Gen Hardware Store | College Capstone Project</span>
                </div>
                <h1 class="hero-title mb-4">
                    Precision Gear for <br>
                    <span class="text-gradient">Engineers & Creators.</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Explore curated 4K displays, mechanical peripherals, spatial acoustics,
                    and intelligent smart home IoT devices with instant ordering and live order tracking.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/products" class="btn btn-primary btn-lg px-4 py-3 fw-bold rounded-pill shadow-lg">
                        <i class="bi bi-grid-fill me-2"></i>Explore Full Catalog
                    </a>
                    <a href="/products?sort=newest" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-pill">
                        <i class="bi bi-lightning-charge me-1"></i>New Arrivals
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="row mt-5 pt-3 border-top border-secondary text-white g-3">
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-warning">24+</div>
                        <div class="small text-muted">Curated Tech Products</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-info">6</div>
                        <div class="small text-muted">Hardware Categories</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">100%</div>
                        <div class="small text-muted">ACID Safe Checkout</div>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Graphic Card -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="card bg-dark border-secondary shadow-2xl rounded-4 p-4 text-white position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold">FEATURED FLAGSHIP</span>
                        <span class="text-warning small"><i class="bi bi-star-fill me-1"></i>4.9 / 5.0</span>
                    </div>
                    <img src="/uploads/products/product-1.svg" alt="UltraSlim 4K OLED Monitor" class="img-fluid rounded-3 mb-3 shadow">
                    <h5 class="fw-bold mb-1">UltraSlim 4K OLED Monitor 27"</h5>
                    <p class="text-muted small mb-3">144Hz refresh rate, 99% DCI-P3 color gamut, and 90W USB-C PD.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-4 fw-bold text-white">$599.99</span>
                        <form action="/cart/add" method="POST" class="ajax-add-cart m-0">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="1">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                <i class="bi bi-bag-plus me-1"></i>Quick Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Browser Section -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small tracking-wide">Product Categories</span>
                <h2 class="fw-bold m-0">Browse by Department</h2>
            </div>
            <a href="/products" class="text-decoration-none text-primary fw-semibold small">View All &rarr;</a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php 
            $catIcons = [
                'electronics' => 'bi-laptop',
                'audio-headphones' => 'bi-headphones',
                'wearables-smartwatches' => 'bi-smartwatch',
                'computer-accessories' => 'bi-keyboard',
                'photography-cameras' => 'bi-camera-video',
                'smart-home-iot' => 'bi-house-gear'
            ];
            foreach ($categories as $cat): 
                $icon = $catIcons[$cat['slug']] ?? 'bi-box';
            ?>
                <div class="col">
                    <a href="/products?category=<?= e($cat['slug']) ?>" class="card h-100 p-3 text-center text-decoration-none border rounded-4 shadow-sm hover-shadow transition">
                        <div class="avatar-lg bg-light text-primary mx-auto mb-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi <?= $icon ?> fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1 small"><?= e($cat['name']) ?></h6>
                        <span class="text-muted small"><?= $cat['product_count'] ?> items</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Hardware Showcase -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small">Top Hardware Picks</span>
                <h2 class="fw-bold m-0">Featured Products</h2>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="/products" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                    View All 24 Products <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col">
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <span class="product-badge-cat"><?= e($product['category_name']) ?></span>
                            <?php if ($product['stock'] > 0): ?>
                                <span class="product-badge-stock badge bg-success"><?= $product['stock'] ?> in stock</span>
                            <?php else: ?>
                                <span class="product-badge-stock badge bg-danger">Out of stock</span>
                            <?php endif; ?>
                            <a href="/products/<?= e($product['slug']) ?>">
                                <img src="<?= upload_url($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="product-body">
                            <h3 class="product-title">
                                <a href="/products/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                            </h3>
                            <p class="product-desc-clamp"><?= e($product['description']) ?></p>
                            <div class="product-footer">
                                <span class="product-price"><?= format_price($product['price']) ?></span>
                                <?php if ($product['stock'] > 0): ?>
                                    <form action="/cart/add" method="POST" class="ajax-add-cart m-0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="bi bi-cart-plus me-1"></i>Add
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Sold Out</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Academic Project Architecture Banner -->
<section class="py-5 bg-dark text-white border-top border-secondary">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold">COLLEGE PROJECT ARCHITECTURE</span>
                <h3 class="fw-bold mb-2">Designed for Comprehensive Viva & Code Inspection</h3>
                <p class="text-muted m-0">
                    Built purely with PHP 8.x, PDO, MySQL, and responsive vanilla components. Features include
                    role-based middleware, tokenized password recovery, guest-to-user cart merging, and ACID transactional checkout.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="/products" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold me-2">
                    Start Shopping
                </a>
                <a href="/login" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                    Admin Sign In
                </a>
            </div>
        </div>
    </div>
</section>

