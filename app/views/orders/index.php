<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Orders</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <div>
            <h1 class="h3 fw-bold m-0 d-flex align-items-center gap-2">
                <i class="bi bi-box-seam text-primary"></i>
                <span>Order History & Tracking</span>
            </h1>
            <p class="text-muted small m-0">View all your previous purchases and real-time shipment status</p>
        </div>
        <a href="/products" class="btn btn-outline-primary btn-sm rounded-pill">
            <i class="bi bi-plus-lg me-1"></i>New Order
        </a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
            <div class="display-1 text-muted mb-3"><i class="bi bi-inbox"></i></div>
            <h3 class="h4 fw-bold">No Orders Placed Yet</h3>
            <p class="text-muted small mb-4">
                You haven't placed any orders with NovaTech Gear yet. Explore our latest tech hardware collection!
            </p>
            <div>
                <a href="/products" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold">
                    Explore Catalog Now
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): 
                $statusBadgeClass = match($order['status']) {
                    'pending'    => 'bg-warning text-dark',
                    'processing' => 'bg-info text-dark',
                    'shipped'    => 'bg-primary text-white',
                    'delivered'  => 'bg-success text-white',
                    'cancelled'  => 'bg-danger text-white',
                    default      => 'bg-secondary text-white'
                };
            ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3 px-4 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <span class="small text-muted d-block">Order Placed</span>
                                    <span class="fw-bold text-dark"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                </div>
                                <div class="vr d-none d-sm-block"></div>
                                <div class="d-none d-sm-block">
                                    <span class="small text-muted d-block">Total</span>
                                    <span class="fw-bold text-dark"><?= format_price($order['total']) ?></span>
                                </div>
                                <div class="vr d-none d-md-block"></div>
                                <div class="d-none d-md-block">
                                    <span class="small text-muted d-block">Payment</span>
                                    <span class="badge bg-secondary text-uppercase"><?= e($order['payment_method']) ?></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 mt-2 mt-sm-0">
                                <span class="badge <?= $statusBadgeClass ?> px-3 py-2 text-uppercase fw-bold rounded-pill small">
                                    <?= e($order['status']) ?>
                                </span>
                                <span class="fw-bold text-muted small">#<?= $order['id'] ?></span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-md-8">
                                    <!-- Preview thumbs -->
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($order['items'] as $item): ?>
                                            <div class="d-flex align-items-center gap-2 border rounded-3 p-2 bg-white" style="max-width: 260px;">
                                                <img src="<?= upload_url($item['product_image']) ?>" alt="" class="rounded bg-dark" style="width: 44px; height: 44px; object-fit: cover;">
                                                <div class="small">
                                                    <div class="fw-bold text-truncate" style="max-width: 180px;"><?= e($item['product_name']) ?></div>
                                                    <span class="text-muted">Qty: <?= $item['quantity'] ?> &times; <?= format_price($item['price_at_purchase']) ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="col-md-4 text-md-end">
                                    <a href="/orders/<?= $order['id'] ?>" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i>Track Order & Timeline &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

