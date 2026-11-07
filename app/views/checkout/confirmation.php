<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Confirmation Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-success text-white p-5 text-center border-0">
                    <div class="rounded-circle bg-white text-success mx-auto mb-3 d-flex align-items-center justify-content-center shadow" style="width: 72px; height: 72px;">
                        <i class="bi bi-check-lg display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">Order Confirmed!</h2>
                    <p class="mb-0 text-white-50">Order #<?= $order['id'] ?> has been placed successfully.</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="alert alert-light border d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <div>
                            <span class="text-muted small d-block">Order Reference:</span>
                            <span class="fw-bold fs-5 text-dark">#<?= $order['id'] ?></span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Date Placed:</span>
                            <span class="fw-semibold text-dark"><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Payment Method:</span>
                            <span class="badge bg-secondary text-uppercase"><?= e($order['payment_method']) ?></span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Initial Status:</span>
                            <span class="badge bg-warning text-dark text-capitalize"><?= e($order['status']) ?></span>
                        </div>
                    </div>

                    <!-- Recipient & Delivery Details -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-uppercase small text-muted mb-2">Delivery Recipient</h6>
                                <p class="mb-1 fw-bold"><?= e($order['shipping_name']) ?></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-telephone me-1"></i><?= e($order['shipping_phone']) ?></p>
                                <p class="mb-0 text-muted small"><i class="bi bi-envelope me-1"></i><?= e($order['customer_email']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-uppercase small text-muted mb-2">Shipping Destination</h6>
                                <p class="mb-0 text-muted small leading-relaxed">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i><?= nl2br(e($order['shipping_address'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items Purchased Table -->
                    <h6 class="fw-bold text-uppercase small text-muted mb-3">Order Line Items</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light small">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center" style="width: 120px;">Price</th>
                                    <th class="text-center" style="width: 80px;">Qty</th>
                                    <th class="text-end" style="width: 140px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= upload_url($item['product_image']) ?>" alt="" class="rounded bg-light" style="width: 48px; height: 48px; object-fit: cover;">
                                                <div>
                                                    <a href="/products/<?= e($item['product_slug']) ?>" class="fw-semibold text-dark text-decoration-none">
                                                        <?= e($item['product_name']) ?>
                                                    </a>
                                                    <span class="small text-muted d-block"><?= e($item['category_name']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-semibold"><?= format_price($item['price_at_purchase']) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?></td>
                                        <td class="text-end fw-bold text-dark"><?= format_price(((float)$item['price_at_purchase']) * ((int)$item['quantity'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Grand Total Paid:</th>
                                    <th class="text-end fs-5 text-primary fw-extrabold"><?= format_price($order['total']) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Action CTAs -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between gap-3">
                        <a href="/orders/<?= $order['id'] ?>" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                            <i class="bi bi-clock-history me-2"></i>Track Order Timeline
                        </a>
                        <a href="/products" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold">
                            <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
