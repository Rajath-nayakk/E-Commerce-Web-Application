<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="/products" class="text-decoration-none">Catalog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h3 fw-bold m-0 d-flex align-items-center gap-2">
            <i class="bi bi-bag-check-fill text-primary"></i>
            <span>Your Shopping Cart</span>
        </h1>
        <?php if (!empty($items)): ?>
            <span class="badge bg-primary rounded-pill px-3 py-2 small">
                <?= $totals['total_quantity'] ?> <?= $totals['total_quantity'] === 1 ? 'Item' : 'Items' ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
        <!-- Empty Cart Display -->
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
            <div class="display-1 text-muted mb-3">
                <i class="bi bi-cart-x"></i>
            </div>
            <h3 class="h4 fw-bold">Your cart is currently empty</h3>
            <p class="text-muted small mb-4">
                Looks like you haven't added any electronics or hardware peripherals to your cart yet.
            </p>
            <div>
                <a href="/products" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-grid me-2"></i>Explore Products Now
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Free Delivery Progress Bar Banner -->
        <?php 
        $freeShippingThreshold = 999.0;
        $diff = $freeShippingThreshold - $totals['subtotal'];
        $progressPercent = min(100, max(0, (int)(($totals['subtotal'] / $freeShippingThreshold) * 100)));
        ?>
        <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-2 small fw-semibold">
                <?php if ($diff > 0): ?>
                    <span>
                        <i class="bi bi-truck text-primary me-1"></i> Add <strong><?= format_price($diff) ?></strong> more for <strong>FREE Express Pan-India Delivery!</strong>
                    </span>
                    <span class="text-muted"><?= $progressPercent ?>%</span>
                <?php else: ?>
                    <span class="text-success fw-bold">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Congratulations! You have unlocked <strong>FREE Express Pan-India Delivery!</strong>
                    </span>
                    <span class="badge bg-success-subtle text-success">Unlocked</span>
                <?php endif; ?>
            </div>
            <div class="delivery-progress-bar">
                <div class="delivery-progress-fill" style="width: <?= $progressPercent ?>%;"></div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Cart Items List -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th scope="col" style="min-width: 240px;">Product</th>
                                    <th scope="col" class="text-center" style="width: 130px;">Price</th>
                                    <th scope="col" class="text-center" style="width: 150px;">Quantity</th>
                                    <th scope="col" class="text-end" style="width: 130px;">Subtotal</th>
                                    <th scope="col" class="text-center" style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): 
                                    $itemSubtotal = ((float)$item['price']) * ((int)$item['quantity']);
                                ?>
                                    <tr id="cart-row-<?= $item['product_id'] ?>">
                                        <!-- Product Info -->
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= upload_url($item['image']) ?>" alt="<?= e($item['name']) ?>" class="rounded-3 shadow-sm bg-light" style="width: 68px; height: 68px; object-fit: cover;">
                                                <div>
                                                    <span class="badge bg-light text-muted small border mb-1"><?= e($item['category_name']) ?></span>
                                                    <h6 class="fw-bold mb-0">
                                                        <a href="/products/<?= e($item['slug']) ?>" class="text-dark text-decoration-none hover-primary">
                                                            <?= e($item['name']) ?>
                                                        </a>
                                                    </h6>
                                                    <span class="small text-muted">Stock: <?= $item['stock'] ?> units</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Unit Price -->
                                        <td class="text-center fw-semibold text-dark">
                                            <?= format_price($item['price']) ?>
                                        </td>

                                        <!-- Quantity Stepper Form -->
                                        <td class="text-center">
                                            <form action="/cart/update" method="POST" class="d-inline-flex align-items-center justify-content-center m-0">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                
                                                <div class="input-group input-group-sm" style="width: 110px;">
                                                    <button class="btn btn-outline-secondary" type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>" title="Decrease">-</button>
                                                    <input type="number" class="form-control text-center px-1" name="quantity_input" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" readonly>
                                                    <button class="btn btn-outline-secondary" type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" title="Increase" <?= ($item['quantity'] >= $item['stock']) ? 'disabled' : '' ?>>+</button>
                                                </div>
                                            </form>
                                        </td>

                                        <!-- Item Subtotal -->
                                        <td class="text-end fw-bold text-dark fs-6">
                                            <?= format_price($itemSubtotal) ?>
                                        </td>

                                        <!-- Delete Item Button -->
                                        <td class="text-center">
                                            <form action="/cart/remove" method="POST" class="m-0" onsubmit="return confirm('Remove this product from cart?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Remove item" aria-label="Remove item">
                                                    <i class="bi bi-trash3 fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between align-items-center p-3 border-top">
                        <a href="/products" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                        </a>
                        <form action="/cart/clear" method="POST" class="m-0" onsubmit="return confirm('Clear entire shopping cart?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold">
                                <i class="bi bi-x-circle me-1"></i>Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 85px;">
                    <h5 class="fw-bold mb-3">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Cart Subtotal (<?= $totals['total_quantity'] ?> items):</span>
                        <span class="fw-bold text-dark"><?= format_price($totals['subtotal']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Pan-India Express Delivery:</span>
                        <?php if ($totals['shipping'] == 0): ?>
                            <span class="fw-bold text-success">FREE</span>
                        <?php else: ?>
                            <span class="fw-semibold text-dark"><?= format_price($totals['shipping']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- GST 18% Inclusive Note -->
                    <?php 
                    $gstAmount = round(($totals['subtotal'] * 18) / 118, 2);
                    ?>
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">GST Included (18%):</span>
                        <span class="text-muted"><?= format_price($gstAmount) ?></span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fs-5 fw-bold text-dark">Grand Total:</span>
                        <span class="fs-4 fw-extrabold text-primary"><?= format_price($totals['total']) ?></span>
                    </div>

                    <div class="d-grid mb-3">
                        <a href="/checkout" class="btn btn-primary btn-lg py-3 fw-bold rounded-3 shadow-md">
                            <i class="bi bi-shield-lock-fill me-2"></i>Proceed to Checkout
                        </a>
                    </div>

                    <div class="text-center small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Safe & Encrypted 256-Bit SSL Checkout
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
