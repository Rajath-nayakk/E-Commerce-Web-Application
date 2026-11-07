<!-- Hero Showcase Banner -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="hero-pill">
                    <i class="bi bi-patch-check-fill text-primary"></i>
                    <span>Authorized Indian Electronics Retailer | 100% Genuine</span>
                </div>
                <h1 class="hero-title mb-4">
                    Engineered for <br>
                    <span class="text-gradient">Creators & Professionals.</span>
                </h1>
                <p class="hero-subtitle mb-4">
                    Discover India's finest lineup of 4K OLED creator displays, Hi-Res ANC audio gear,
                    precision titanium wearables, and high-efficiency smart workstation hardware.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/products" class="btn btn-primary btn-lg px-4 py-3 fw-bold rounded-pill shadow-lg">
                        <i class="bi bi-grid-fill me-2"></i>Explore Full Catalog
                    </a>
                    <a href="/products?deal=1" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold rounded-pill">
                        <i class="bi bi-fire text-danger me-1"></i>Today's Deals
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="row mt-5 pt-3 border-top border-secondary text-white g-3">
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-white">24+</div>
                        <div class="small text-muted">Curated Electronics</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-info">19,000+</div>
                        <div class="small text-muted">PIN Codes Served</div>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">100%</div>
                        <div class="small text-muted">Genuine Brand Warranty</div>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Graphic Card -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="card bg-dark border-secondary shadow-2xl rounded-4 p-4 text-white position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold">FLAGSHIP PICK</span>
                        <span class="text-warning small"><i class="bi bi-star-fill me-1"></i>4.9 / 5.0 (142 reviews)</span>
                    </div>
                    <div class="bg-black rounded-3 overflow-hidden mb-3 text-center p-2">
                        <img src="<?= upload_url('monitor-4k-oled.jpg') ?>" alt="UltraSlim 4K OLED Monitor" class="img-fluid rounded-3 shadow" style="max-height: 240px; object-fit: contain;">
                    </div>
                    <span class="text-uppercase small text-muted fw-bold">NovaTech Pro</span>
                    <h5 class="fw-bold mb-1">UltraSlim 4K OLED Monitor 27"</h5>
                    <p class="text-muted small mb-3">144Hz refresh rate, 99% DCI-P3 color gamut, and 90W USB-C Power Delivery.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fs-4 fw-bold text-white"><?= format_price(54990) ?></span>
                            <span class="text-muted text-decoration-line-through small ms-2"><?= format_price(69990) ?></span>
                        </div>
                        <form action="/cart/add" method="POST" class="ajax-add-cart m-0">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="1">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                <i class="bi bi-bag-plus me-1"></i>Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Pillars Bar -->
<section class="trust-pillars-bar">
    <div class="container">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
            <div class="col">
                <div class="trust-pillar-item">
                    <div class="trust-pillar-icon"><i class="bi bi-truck"></i></div>
                    <div>
                        <div class="trust-pillar-title">Free Express Delivery</div>
                        <p class="trust-pillar-desc">Pan-India air dispatch on orders over ₹999</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="trust-pillar-item">
                    <div class="trust-pillar-icon"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <div class="trust-pillar-title">1-Year Official Warranty</div>
                        <p class="trust-pillar-desc">Doorstep brand repairs & genuine spare parts</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="trust-pillar-item">
                    <div class="trust-pillar-icon"><i class="bi bi-arrow-repeat"></i></div>
                    <div>
                        <div class="trust-pillar-title">7-Day Easy Replacement</div>
                        <p class="trust-pillar-desc">Instant pickup for any hardware transit issues</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="trust-pillar-item">
                    <div class="trust-pillar-icon"><i class="bi bi-receipt"></i></div>
                    <div>
                        <div class="trust-pillar-title">Verified GST Invoice</div>
                        <p class="trust-pillar-desc">Eligible for business input tax credit (ITC)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Showcase Grid -->
<section class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small tracking-wide">Featured Categories</span>
                <h2 class="fw-bold m-0">Browse by Hardware Department</h2>
            </div>
            <a href="/products" class="text-decoration-none text-primary fw-semibold small">View All Catalog &rarr;</a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php 
            $catImages = [
                'electronics'            => 'cat-laptops.jpg',
                'audio-headphones'       => 'cat-audio.jpg',
                'wearables-smartwatches' => 'cat-wearables.jpg',
                'computer-accessories'   => 'cat-accessories.jpg',
                'photography-cameras'    => 'cat-monitors.jpg',
                'smart-home-iot'         => 'cat-gaming.jpg'
            ];
            foreach ($categories as $cat): 
                $imgName = $catImages[$cat['slug']] ?? 'cat-accessories.jpg';
            ?>
                <div class="col">
                    <a href="/products?category=<?= e($cat['slug']) ?>" class="category-card">
                        <img src="<?= upload_url($imgName, 'categories') ?>" alt="<?= e($cat['name']) ?>" class="category-card-img" loading="lazy">
                        <div class="category-card-overlay">
                            <h6 class="category-card-title"><?= e($cat['name']) ?></h6>
                            <span class="category-card-count"><?= $cat['product_count'] ?> products</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Deals of the Day Section (If Deals Available) -->
<?php if (!empty($deals)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small fw-bold mb-1">
                    <i class="bi bi-lightning-charge-fill me-1"></i>LIMITED TIME OFFERS
                </span>
                <h2 class="fw-bold m-0">Deals of the Day</h2>
            </div>
            <a href="/products?deal=1" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold">
                Explore All Deals <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <?php foreach ($deals as $dealProduct): 
                $disc = discount_percent($dealProduct['original_price'] ?? null, $dealProduct['price']);
            ?>
                <div class="col">
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <span class="product-badge-cat"><?= e($dealProduct['category_name']) ?></span>
                            <?php if ($disc > 0): ?>
                                <span class="product-badge-discount">SAVE <?= $disc ?>%</span>
                            <?php endif; ?>
                            <a href="/products/<?= e($dealProduct['slug']) ?>">
                                <img src="<?= upload_url($dealProduct['image']) ?>" alt="<?= e($dealProduct['name']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="product-body">
                            <div class="product-brand"><?= e($dealProduct['brand'] ?? 'NovaTech') ?></div>
                            <h3 class="product-title">
                                <a href="/products/<?= e($dealProduct['slug']) ?>"><?= e($dealProduct['name']) ?></a>
                            </h3>
                            <div class="product-rating-row">
                                <span class="product-rating-badge">
                                    <i class="bi bi-star-fill"></i> <?= number_format((float) ($dealProduct['rating'] ?? 4.8), 1) ?>
                                </span>
                                <span class="text-muted small">(<?= $dealProduct['review_count'] ?? 50 ?>)</span>
                            </div>
                            <div class="product-pricing">
                                <span class="product-price-current"><?= format_price($dealProduct['price']) ?></span>
                                <?php if (!empty($dealProduct['original_price']) && $dealProduct['original_price'] > $dealProduct['price']): ?>
                                    <span class="product-price-mrp"><?= format_price($dealProduct['original_price']) ?></span>
                                    <span class="product-savings">You Save <?= format_price($dealProduct['original_price'] - $dealProduct['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-footer">
                                <?php if ($dealProduct['stock'] > 0): ?>
                                    <form action="/cart/add" method="POST" class="ajax-add-cart w-100 m-0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= $dealProduct['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>Sold Out</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Hardware Showcase -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
            <div>
                <span class="text-primary fw-bold text-uppercase small">Flagship Electronics</span>
                <h2 class="fw-bold m-0">Featured Products</h2>
            </div>
            <div class="mt-2 mt-md-0">
                <a href="/products" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                    View All 24 Products <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($featuredProducts as $product): 
                $disc = discount_percent($product['original_price'] ?? null, $product['price']);
            ?>
                <div class="col">
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <span class="product-badge-cat"><?= e($product['category_name']) ?></span>
                            <?php if ($disc > 0): ?>
                                <span class="product-badge-discount">SAVE <?= $disc ?>%</span>
                            <?php endif; ?>
                            <?php if ($product['stock'] > 0 && $product['stock'] <= 5): ?>
                                <span class="product-badge-stock badge bg-warning text-dark">Only <?= $product['stock'] ?> left</span>
                            <?php elseif ($product['stock'] <= 0): ?>
                                <span class="product-badge-stock badge bg-danger">Out of stock</span>
                            <?php endif; ?>
                            <a href="/products/<?= e($product['slug']) ?>">
                                <img src="<?= upload_url($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="product-body">
                            <div class="product-brand"><?= e($product['brand'] ?? 'NovaTech') ?></div>
                            <h3 class="product-title">
                                <a href="/products/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                            </h3>
                            <div class="product-rating-row">
                                <span class="product-rating-badge">
                                    <i class="bi bi-star-fill"></i> <?= number_format((float) ($product['rating'] ?? 4.8), 1) ?>
                                </span>
                                <span class="text-muted small">(<?= $product['review_count'] ?? 100 ?> reviews)</span>
                            </div>
                            <div class="product-pricing">
                                <span class="product-price-current"><?= format_price($product['price']) ?></span>
                                <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <span class="product-price-mrp"><?= format_price($product['original_price']) ?></span>
                                    <span class="product-savings">Save <?= format_price($product['original_price'] - $product['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-footer">
                                <?php if ($product['stock'] > 0): ?>
                                    <form action="/cart/add" method="POST" class="ajax-add-cart w-100 m-0">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-add-cart">
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>Sold Out</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Academic Project Architecture Banner for Viva -->
<section class="py-5 bg-dark text-white border-top border-secondary">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold">COLLEGE PROJECT ARCHITECTURE</span>
                <h3 class="fw-bold mb-2">Clean MVC Architecture & Pure PHP 8.x Engine</h3>
                <p class="text-muted m-0 leading-relaxed">
                    Zero third-party frameworks. Crafted with pure PDO prepared statements, CSRF protection,
                    ACID stock reservation transactions, guest-to-user session cart merge, and live 4-stage order lifecycle tracking.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="/products" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold me-2 shadow-sm">
                    Browse Catalog
                </a>
                <a href="/login" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                    Admin Sign In
                </a>
            </div>
        </div>
    </div>
</section>
