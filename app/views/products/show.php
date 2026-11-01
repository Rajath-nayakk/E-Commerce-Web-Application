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
            <div class="col-lg-6 bg-dark d-flex align-items-center justify-content-center p-4 p-md-5 position-relative">
                <span class="position-absolute top-0 start-0 m-3 badge bg-primary px-3 py-2 rounded-pill small">
                    <?= e($product['category_name']) ?>
                </span>
                <img src="<?= upload_url($product['image']) ?>" alt="<?= e($product['name']) ?>" class="img-fluid rounded-3 shadow-lg" style="max-height: 480px; width: 100%; object-fit: contain;">
            </div>

            <!-- Product Info Column -->
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between bg-white">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-light text-muted border small">SKU: <?= e($product['slug']) ?></span>
                        <?php if ($product['stock'] > 5): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                                <i class="bi bi-check-circle-fill me-1"></i> In Stock (<?= $product['stock'] ?> units)
                            </span>
                        <?php elseif ($product['stock'] > 0): ?>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Low Stock (Only <?= $product['stock'] ?> left!)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill small">
                                <i class="bi bi-x-circle-fill me-1"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="h2 fw-bold text-dark mb-3"><?= e($product['name']) ?></h1>

                    <div class="fs-2 fw-extrabold text-primary mb-4">
                        <?= format_price($product['price']) ?>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-uppercase small text-muted mb-2">Description & Specifications</h6>
                    <p class="text-secondary leading-relaxed mb-4">
                        <?= nl2br(e($product['description'])) ?>
                    </p>

                    <!-- Feature Highlights -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-shield-check text-success fs-5"></i>
                                <span>2-Year Manufacturer Warranty</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 p-2 rounded bg-light small">
                                <i class="bi bi-truck text-primary fs-5"></i>
                                <span>Fast Tracked Dispatch</span>
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
                                    <label for="quantitySelect" class="form-label small fw-semibold text-muted m-0">Quantity:</label>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group" style="width: 130px;">
                                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(-1)">-</button>
                                        <input type="number" class="form-control text-center" id="quantityInput" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="adjustQty(1)">+</button>
                                    </div>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-3 shadow">
                                        <i class="bi bi-bag-plus-fill me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger d-flex align-items-center m-0">
                            <i class="bi bi-exclamation-octagon-fill fs-5 me-2"></i>
                            <div>This product is currently out of stock. Please check back later or browse related items below.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h3 class="h4 fw-bold mb-4">Related Hardware in <?= e($product['category_name']) ?></h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="col">
                        <div class="product-card">
                            <div class="product-image-wrap">
                                <span class="product-badge-cat"><?= e($rel['category_name']) ?></span>
                                <a href="/products/<?= e($rel['slug']) ?>">
                                    <img src="<?= upload_url($rel['image']) ?>" alt="<?= e($rel['name']) ?>" loading="lazy">
                                </a>
                            </div>
                            <div class="product-body">
                                <h3 class="product-title">
                                    <a href="/products/<?= e($rel['slug']) ?>"><?= e($rel['name']) ?></a>
                                </h3>
                                <div class="product-footer">
                                    <span class="product-price"><?= format_price($rel['price']) ?></span>
                                    <a href="/products/<?= e($rel['slug']) ?>" class="btn btn-outline-primary btn-sm rounded-pill">View</a>
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
function adjustQty(delta) {
    const input = document.getElementById('quantityInput');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val += delta;
    const max = parseInt(input.getAttribute('max')) || 999;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
}
</script>
