<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="/orders" class="text-decoration-none">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Track Order #<?= $order['id'] ?></li>
        </ol>
    </nav>

    <!-- Header info banner -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-bold m-0">Order #<?= $order['id'] ?></h1>
                <?php 
                $statusBadgeClass = match($order['status']) {
                    'pending'    => 'bg-warning text-dark',
                    'processing' => 'bg-info text-dark',
                    'shipped'    => 'bg-primary text-white',
                    'delivered'  => 'bg-success text-white',
                    'cancelled'  => 'bg-danger text-white',
                    default      => 'bg-secondary text-white'
                };
                ?>
                <span class="badge <?= $statusBadgeClass ?> px-3 py-1 rounded-pill text-uppercase fw-bold">
                    <?= e($order['status']) ?>
                </span>
            </div>
            <p class="text-muted small m-0">
                Placed on <?= date('F d, Y \a\t h:i A', strtotime($order['created_at'])) ?> &bull; 
                Paid via <?= strtoupper(e($order['payment_method'])) ?>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="/orders" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>All Orders
            </a>
            <?php if (\App\Core\Auth::isAdmin()): ?>
                <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-outline-warning btn-sm rounded-pill">
                    <i class="bi bi-gear-fill me-1"></i>Admin Edit Status
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Tracking Timeline Graphic -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
        <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt-fill text-primary"></i>
            <span>Real-Time Shipment Journey</span>
        </h5>

        <?php if ($order['status'] === 'cancelled'): ?>
            <!-- Cancelled Notice -->
            <div class="alert alert-danger d-flex align-items-center rounded-3 p-3 mb-0">
                <i class="bi bi-x-octagon-fill fs-3 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">Order Cancelled</h6>
                    <p class="small mb-0">This order was cancelled. Reserved inventory was restored to available stock.</p>
                </div>
            </div>
        <?php else: 
            $stages = [
                'pending'    => ['title' => 'Order Placed', 'desc' => 'Confirmed & logged in system', 'icon' => 'bi-receipt'],
                'processing' => ['title' => 'Processing', 'desc' => 'Inspected & packaged at depot', 'icon' => 'bi-box-seam'],
                'shipped'    => ['title' => 'Shipped', 'desc' => 'Handed over to carrier', 'icon' => 'bi-truck'],
                'delivered'  => ['title' => 'Delivered', 'desc' => 'Completed & received', 'icon' => 'bi-house-check'],
            ];

            $stageKeys = array_keys($stages);
            $currentIndex = array_search($order['status'], $stageKeys, true);
            if ($currentIndex === false) $currentIndex = 0;
            $progressPercent = ($currentIndex / 3) * 100;
        ?>
            <!-- 4-Stage Graphical Timeline -->
            <div class="order-timeline">
                <div class="timeline-progress-bar">
                    <div class="timeline-progress-fill" style="width: <?= $progressPercent ?>%;"></div>
                </div>

                <div class="row g-2">
                    <?php foreach ($stageKeys as $idx => $stKey): 
                        $info = $stages[$stKey];
                        $isCompleted = ($idx < $currentIndex);
                        $isActive = ($idx === $currentIndex);
                        $stepClass = $isCompleted ? 'completed' : ($isActive ? 'active' : 'upcoming');
                    ?>
                        <div class="col-3 timeline-step <?= $stepClass ?>">
                            <div class="timeline-icon">
                                <?php if ($isCompleted): ?>
                                    <i class="bi bi-check-lg"></i>
                                <?php else: ?>
                                    <i class="bi <?= $info['icon'] ?>"></i>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold mb-1 small <?= $isActive ? 'text-primary' : ($isCompleted ? 'text-success' : 'text-muted') ?>">
                                <?= $info['title'] ?>
                            </h6>
                            <p class="small text-muted d-none d-md-block m-0" style="font-size: 0.75rem;">
                                <?= $info['desc'] ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Chronological Audit Log (History records) -->
        <div class="mt-4 pt-4 border-top">
            <h6 class="text-uppercase small fw-bold text-muted mb-3">Status Event History (Audit Trail)</h6>
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

    <!-- Order Details & Shipping Information -->
    <div class="row g-4">
        <!-- Purchased Line Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="fw-bold m-0">Purchased Hardware (<?= count($order['items']) ?> items)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>Product</th>
                                <th class="text-center" style="width: 120px;">Price</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 120px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= upload_url($item['product_image']) ?>" alt="" class="rounded bg-dark" style="width: 48px; height: 48px; object-fit: cover;">
                                            <div>
                                                <a href="/products/<?= e($item['product_slug']) ?>" class="fw-semibold text-dark text-decoration-none hover-primary">
                                                    <?= e($item['product_name']) ?>
                                                </a>
                                                <span class="d-block small text-muted"><?= e($item['category_name']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= format_price($item['price_at_purchase']) ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end fw-bold text-primary"><?= format_price(((float)$item['price_at_purchase']) * ((int)$item['quantity'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Amount:</th>
                                <th class="text-end fs-5 text-primary"><?= format_price($order['total']) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recipient & Delivery Info Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3">Delivery Destination</h6>
                <div class="mb-3">
                    <span class="small text-muted d-block">Recipient:</span>
                    <strong class="text-dark"><?= e($order['shipping_name']) ?></strong>
                </div>
                <div class="mb-3">
                    <span class="small text-muted d-block">Contact Phone:</span>
                    <span class="text-dark"><?= e($order['shipping_phone']) ?></span>
                </div>
                <div class="mb-3">
                    <span class="small text-muted d-block">Shipping Address:</span>
                    <span class="text-secondary small leading-relaxed"><?= nl2br(e($order['shipping_address'])) ?></span>
                </div>
                <hr>
                <div>
                    <span class="small text-muted d-block">Payment Mode:</span>
                    <span class="badge bg-secondary text-uppercase"><?= e($order['payment_method']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

