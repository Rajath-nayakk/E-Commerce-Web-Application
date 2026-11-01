<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h4 class="fw-bold m-0">Manage Order #<?= $order['id'] ?></h4>
        <span class="small text-muted">Customer: <?= e($order['customer_name']) ?> (<?= e($order['customer_email']) ?>)</span>
    </div>
    <a href="/admin/orders" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i>Back to Orders
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Status Update Form & Timeline -->
    <div class="col-lg-7">
        <!-- Status Transition Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-repeat text-primary"></i>
                <span>Update Fulfillment Status</span>
            </h5>

            <form action="/admin/orders/<?= $order['id'] ?>/status" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="status" class="form-label small fw-semibold text-muted">New Status</label>
                    <select class="form-select form-select-lg fw-bold" id="status" name="status" required>
                        <?php foreach ($statusOptions as $key => $lbl): ?>
                            <option value="<?= $key ?>" <?= ($order['status'] === $key) ? 'selected' : '' ?>>
                                <?= $lbl ?> <?= ($order['status'] === $key) ? '(Current)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="note" class="form-label small fw-semibold text-muted">Audit Note / Carrier Tracking ID (Optional)</label>
                    <input type="text" class="form-control" id="note" name="note" placeholder="e.g. Dispatched via FedEx tracking #98218734">
                    <div class="form-text small text-muted">This note will appear directly in the customer's live tracking timeline.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Save & Transition Status
                    </button>
                </div>
            </form>
        </div>

        <!-- Chronological Timeline Display -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h6 class="fw-bold text-uppercase small text-muted mb-3">Status Journey Audit Trail</h6>
            <div class="list-group list-group-flush">
                <?php foreach ($order['timeline'] as $hist): ?>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-start border-0">
                        <div class="d-flex align-items-start gap-3">
                            <span class="badge bg-secondary-subtle text-secondary border mt-1">
                                <i class="bi bi-check2"></i>
                            </span>
                            <div>
                                <span class="fw-bold text-dark text-capitalize"><?= e($hist['status']) ?></span>
                                <?php if (!empty($hist['note'])): ?>
                                    <p class="small text-muted mb-0"><?= e($hist['note']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="small text-muted text-nowrap">
                            <?= date('M d, Y h:i A', strtotime($hist['created_at'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Shipping & Order Summary -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h5 class="fw-bold mb-3">Customer & Destination</h5>
            <div class="mb-3">
                <span class="small text-muted d-block">Recipient Name:</span>
                <strong class="text-dark"><?= e($order['shipping_name']) ?></strong>
            </div>
            <div class="mb-3">
                <span class="small text-muted d-block">Phone:</span>
                <span class="text-dark"><?= e($order['shipping_phone']) ?></span>
            </div>
            <div class="mb-3">
                <span class="small text-muted d-block">Shipping Address:</span>
                <span class="text-secondary small leading-relaxed"><?= nl2br(e($order['shipping_address'])) ?></span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
                <div>
                    <span class="small text-muted d-block">Payment Method:</span>
                    <span class="badge bg-secondary text-uppercase"><?= e($order['payment_method']) ?></span>
                </div>
                <div>
                    <span class="small text-muted d-block">Current Status:</span>
                    <span class="badge bg-warning text-dark text-capitalize"><?= e($order['status']) ?></span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold mb-3">Order Items (<?= count($order['items']) ?>)</h5>
            <div class="list-group list-group-flush mb-3">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= upload_url($item['product_image']) ?>" alt="" class="rounded bg-dark" style="width: 36px; height: 36px; object-fit: cover;">
                            <div class="small">
                                <div class="fw-semibold text-truncate" style="max-width: 170px;"><?= e($item['product_name']) ?></div>
                                <span class="text-muted">Qty: <?= $item['quantity'] ?></span>
                            </div>
                        </div>
                        <span class="small fw-bold"><?= format_price(((float)$item['price_at_purchase']) * ((int)$item['quantity'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Total:</span>
                <span class="fs-4 fw-extrabold text-primary"><?= format_price($order['total']) ?></span>
            </div>
        </div>
    </div>
</div>
