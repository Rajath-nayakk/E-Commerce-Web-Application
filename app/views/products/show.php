<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="/products" class="text-decoration-none">Catalog</a></li>
            <li class="breadcrumb-item"><a href="/products?category=<?= e($product['category_slug']) ?>" class="text-decoration-none"><?= e($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($product['name']) ?></li>
        </ol>
    </nav>

    <!-- Product Showcase Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="row g-0">
            <!-- Product Image Column -->
            <div class="col-lg-6 product-detail-hero-img p-4 p-md-5">
                <span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2 rounded-pill small">
                    <?= e($product['category_name']) ?>
                </span>
                <?php 
                $disc = discount_percent($product['original_price'] ?? null, $product['price']);
                if ($disc > 0): ?>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-success px-3 py-2 rounded-pill small fw-bold">
                        SAVE <?= $disc ?>%
                    </span>
                <?php endif; ?>
                <img src="<?= upload_url($product['image']) ?>" alt="<?= e($product['name']) ?>" class="img-fluid rounded-3 shadow-md" id="mainProductImg">
            </div>

            <!-- Product Info Column -->
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between bg-white">
                <div>
                    <!-- Brand & SKU -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase small fw-bold text-muted tracking-wide"><?= e($product['brand'] ?? 'NovaTech Gear') ?></span>
                        <span class="badge bg-light text-muted border small">SKU: <?= e($product['slug']) ?></span>
                    </div>

                    <!-- Title -->
                    <h1 class="h2 fw-bold text-dark mb-2"><?= e($product['name']) ?></h1>

                    <!-- Rating & Reviews -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-success text-white px-2 py-1 rounded small fw-bold">
                            ★ <?= number_format((float) ($product['rating'] ?? 4.8), 1) ?>
                        </span>
                        <span class="text-muted small"><?= $product['review_count'] ?? 120 ?> Verified Customer Ratings & Reviews</span>
                    </div>

                    <!-- Pricing Block (INR) -->
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex align-items-baseline gap-2">
                            <span class="fs-2 fw-extrabold text-dark"><?= format_price($product['price']) ?></span>
                            <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                <span class="fs-6 text-muted text-decoration-line-through"><?= format_price($product['original_price']) ?></span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small fw-bold">
                                    Save <?= format_price($product['original_price'] - $product['price']) ?> (<?= $disc ?>% OFF)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="small text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i> Inclusive of all taxes (18% GST). Input Tax Credit invoice provided.
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-4">
                        <?php if ($product['stock'] > 5): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> In Stock (<?= $product['stock'] ?> units) &mdash; Ready to Dispatch
                            </span>
                        <?php elseif ($product['stock'] > 0): ?>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill small fw-semibold">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Only <?= $product['stock'] ?> units left in stock!
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small fw-semibold">
                                <i class="bi bi-x-circle-fill me-1"></i> Currently Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Interactive Indian PIN Code Delivery Checker -->
                    <div class="pincode-checker-box mb-4">
                        <label for="pincodeInput" class="form-label small fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-geo-alt-fill text-danger"></i> Check Pan-India Delivery & COD Availability
                        </label>
                        <div class="input-group input-group-sm mb-2" style="max-width: 320px;">
                            <input type="text" class="form-control" id="pincodeInput" placeholder="Enter 6-digit PIN (e.g. 560001)" maxlength="6" value="560001">
                            <button class="btn btn-primary fw-semibold" type="button" onclick="checkPincode()">Check</button>
                        </div>
                        <div id="pincodeFeedback" class="small text-success fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i> Express delivery available to Bengaluru 560001 within 2 business days. COD available.
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-uppercase small text-muted mb-2">Product Overview</h6>
                        <p class="text-secondary leading-relaxed m-0">
                            <?= nl2br(e($product['description'])) ?>
                        </p>
                    </div>

                    <!-- 4 Trust Badges -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-shield-check text-success fs-5"></i>
                                <span>1-Year Brand Warranty</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-arrow-repeat text-warning fs-5"></i>
                                <span>7-Day Replacement</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-truck text-primary fs-5"></i>
                                <span>Free Pan-India Delivery</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-cash-stack text-success fs-5"></i>
                                <span>Cash on Delivery</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <div class="pt-4 border-top">
                    <?php if ($product['stock'] > 0): ?>
                        <form action="/cart/add" method="POST" class="ajax-add-cart">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="quantityInput" class="form-label small fw-semibold text-muted m-0">Quantity:</label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group" style="width: 130px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                                        <input type="number" class="form-control text-center" id="quantityInput" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                                    </div>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 shadow-md">
                                        <i class="bi bi-bag-plus-fill me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center m-0">
                            <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
                            <div><strong>Sold Out:</strong> This hardware configuration is temporarily unavailable. Check back soon!</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5 pt-3">
            <h3 class="h4 fw-bold mb-4">Recommended Hardware in this Category</h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php foreach ($relatedProducts as $related): 
                    $relDisc = discount_percent($related['original_price'] ?? null, $related['price']);
                ?>
                    <div class="col">
                        <div class="product-card">
                            <div class="product-image-wrap">
                                <span class="product-badge-cat"><?= e($related['category_name']) ?></span>
                                <?php if ($relDisc > 0): ?>
                                    <span class="product-badge-discount">SAVE <?= $relDisc ?>%</span>
                                <?php endif; ?>
                                <a href="/products/<?= e($related['slug']) ?>">
                                    <img src="<?= upload_url($related['image']) ?>" alt="<?= e($related['name']) ?>" loading="lazy">
                                </a>
                            </div>
                            <div class="product-body">
                                <div class="product-brand"><?= e($related['brand'] ?? 'NovaTech') ?></div>
                                <h3 class="product-title">
                                    <a href="/products/<?= e($related['slug']) ?>"><?= e($related['name']) ?></a>
                                </h3>
                                <div class="product-pricing">
                                    <span class="product-price-current"><?= format_price($related['price']) ?></span>
                                    <?php if (!empty($related['original_price']) && $related['original_price'] > $related['price']): ?>
                                        <span class="product-price-mrp"><?= format_price($related['original_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-footer">
                                    <a href="/products/<?= e($related['slug']) ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-semibold">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function adjustQty(amount) {
    const input = document.getElementById('quantityInput');
    if (!input) return;
    let current = parseInt(input.value) || 1;
    let min = parseInt(input.min) || 1;
    let max = parseInt(input.max) || 999;
    let next = current + amount;
    if (next >= min && next <= max) {
        input.value = next;
    }
}

function checkPincode() {
    const input = document.getElementById('pincodeInput');
    const feedback = document.getElementById('pincodeFeedback');
    const pin = (input ? input.value : '').trim();
    if (!/^\d{6}$/.test(pin)) {
        feedback.className = 'small text-danger fw-semibold';
        feedback.innerHTML = '<i class="bi bi-x-circle me-1"></i> Please enter a valid 6-digit Indian PIN code.';
        return;
    }
    feedback.className = 'small text-success fw-semibold';
    feedback.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Express delivery available to PIN ' + pin + ' within 2-3 business days. COD & Online payment accepted.';
}
</script>
