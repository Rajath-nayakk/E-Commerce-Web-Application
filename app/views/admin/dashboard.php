<!-- Overview Metric Cards -->
<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle text-success">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Total Revenue</span>
                <h3 class="fw-bold m-0 text-dark"><?= format_price($metrics['total_sales']) ?></h3>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle text-primary">
                <i class="bi bi-bag-check-fill"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Total Orders</span>
                <h3 class="fw-bold m-0 text-dark"><?= $metrics['total_orders'] ?></h3>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle text-info">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Hardware Catalog</span>
                <h3 class="fw-bold m-0 text-dark"><?= $metrics['total_products'] ?> items</h3>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-sm-6 col-xl-3">
        <div class="admin-stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <span class="text-muted small fw-semibold d-block">Registered Customers</span>
                <h3 class="fw-bold m-0 text-dark"><?= $metrics['total_users'] ?> users</h3>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Alert Banner (if any) -->
<?php if (!empty($metrics['low_stock_products'])): ?>
    <div class="alert alert-warning border-warning shadow-sm rounded-4 p-3 mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-4 text-warning"></i>
                <div>
                    <strong class="d-block">Low Inventory Alert (<?= count($metrics['low_stock_products']) ?> products)</strong>
                    <span class="small text-muted">The following items have 5 or fewer units remaining in stock:</span>
                </div>
            </div>
            <a href="/admin/products" class="btn btn-warning btn-sm fw-semibold rounded-pill">Manage Inventory</a>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top border-warning-subtle">
            <?php foreach ($metrics['low_stock_products'] as $lsp): ?>
                <span class="badge bg-white text-dark border small p-2">
                    <?= e($lsp['name']) ?> &mdash; <strong class="text-danger"><?= $lsp['stock'] ?> left</strong>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold m-0">Recent Customer Orders</h5>
                <a href="/admin/orders" class="small fw-semibold text-primary text-decoration-none">View All Orders &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($metrics['recent_orders'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No orders placed yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($metrics['recent_orders'] as $ord): 
                                $statusBadgeClass = match($ord['status']) {
                                    'pending'    => 'bg-warning text-dark',
                                    'processing' => 'bg-info text-dark',
                                    'shipped'    => 'bg-primary text-white',
                                    'delivered'  => 'bg-success text-white',
                                    'cancelled'  => 'bg-danger text-white',
                                    default      => 'bg-secondary text-white'
                                };
                            ?>
                                <tr>
                                    <td><strong class="text-dark">#<?= $ord['id'] ?></strong></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= e($ord['customer_name']) ?></div>
                                        <span class="small text-muted"><?= e($ord['customer_email']) ?></span>
                                    </td>
                                    <td class="small text-muted"><?= date('M d, Y', strtotime($ord['created_at'])) ?></td>
                                    <td>
                                        <span class="badge <?= $statusBadgeClass ?> text-capitalize px-2 py-1">
                                            <?= e($ord['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark"><?= format_price($ord['total']) ?></td>
                                    <td class="text-center">
                                        <a href="/admin/orders/<?= $ord['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Status Breakdown -->
    <div class="col-lg-4">
        <!-- Quick Actions Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="/admin/products/create" class="btn btn-primary fw-semibold rounded-3 py-2 text-start">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </a>
                <a href="/admin/categories" class="btn btn-outline-secondary fw-semibold rounded-3 py-2 text-start">
                    <i class="bi bi-tag me-2"></i>Manage Categories
                </a>
                <a href="/admin/orders?status=pending" class="btn btn-outline-warning text-dark fw-semibold rounded-3 py-2 text-start">
                    <i class="bi bi-hourglass-split me-2"></i>Review Pending Orders
                </a>
            </div>
        </div>

        <!-- Order Breakdown -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-3">Orders Breakdown</h5>
            <?php if (empty($metrics['status_breakdown'])): ?>
                <p class="small text-muted m-0">No order status data available.</p>
            <?php else: ?>
                <ul class="list-group list-group-flush small">
                    <?php foreach ($metrics['status_breakdown'] as $sb): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                            <span class="text-capitalize fw-semibold text-secondary">
                                <i class="bi bi-circle-fill me-2 fs-6" style="font-size: 8px !important;"></i>
                                <?= e($sb['status']) ?>
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill"><?= $sb['count'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
