<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h4 class="fw-bold m-0">Registered Users & Customers</h4>
        <p class="text-muted small m-0">User accounts, roles, registration dates, and purchase volume</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th style="width: 70px;">#ID</th>
                    <th>User / Customer</th>
                    <th>Email</th>
                    <th class="text-center">Role</th>
                    <th>Joined</th>
                    <th class="text-center">Orders</th>
                    <th class="text-end">Total Spent</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-bold text-dark"><?= e($user['name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="small text-muted font-monospace"><?= e($user['email']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="badge bg-danger text-uppercase px-3 py-1 rounded-pill small">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-primary text-uppercase px-3 py-1 rounded-pill small">Customer</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill"><?= $user['total_orders'] ?> orders</span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                <?= format_price($user['total_spent']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

