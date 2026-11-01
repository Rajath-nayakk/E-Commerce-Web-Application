<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold m-0">Customer Orders</h4>
        <p class="text-muted small m-0">Review placed orders, shipping addresses, and advance order status</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form action="/admin/orders" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by Order ID, customer, or email..." value="<?= e($searchQuery) ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach ($statusOptions as $key => $lbl): ?>
                    <option value="<?= $key ?>" <?= ($statusFilter === $key) ? 'selected' : '' ?>>
                        <?= $lbl ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary btn-sm flex-grow-1">Filter</button>
            <?php if (!empty($searchQuery) || !empty($statusFilter)): ?>
                <a href="/admin/orders" class="btn btn-outline-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th style="width: 80px;">Order</th>
                    <th>Customer & Contact</th>
                    <th>Date Placed</th>
                    <th class="text-center">Items</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Payment</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No orders found matching the filter criteria.</td>
                    </tr>
                <?php else: ?>
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
                        <tr>
                            <td><strong class="text-dark">#<?= $order['id'] ?></strong></td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($order['shipping_name']) ?></div>
                                <span class="small text-muted"><?= e($order['customer_email']) ?></span>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill"><?= $order['item_count'] ?> items</span>
                            </td>
                            <td class="text-end fw-bold text-dark"><?= format_price($order['total']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-secondary text-uppercase small"><?= e($order['payment_method']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $statusBadgeClass ?> text-capitalize px-3 py-1 rounded-pill small">
                                    <?= e($order['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="/admin/orders/<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
