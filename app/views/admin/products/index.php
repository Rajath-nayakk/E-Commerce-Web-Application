<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold m-0">Hardware Products</h4>
        <p class="text-muted small m-0">Manage catalog pricing, stock inventory, and images</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/products/create" class="btn btn-primary rounded-pill px-4 fw-semibold">
            <i class="bi bi-plus-lg me-1"></i>Add Product
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
    <form action="/admin/products" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Search by product name..." value="<?= e($filters['q'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-4">
            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (($filters['category'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary btn-sm flex-grow-1">Filter</button>
            <?php if (!empty($filters['q']) || !empty($filters['category'])): ?>
                <a href="/admin/products" class="btn btn-outline-secondary btn-sm">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th style="width: 70px;">Image</th>
                    <th>Product Name & Slug</th>
                    <th>Category</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No products found matching criteria.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="<?= upload_url($p['image']) ?>" alt="" class="rounded bg-dark shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($p['name']) ?></div>
                                <span class="small text-muted font-monospace"><?= e($p['slug']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border small"><?= e($p['category_name']) ?></span>
                            </td>
                            <td class="text-end fw-semibold"><?= format_price($p['price']) ?></td>
                            <td class="text-center">
                                <?php if ($p['stock'] <= 5): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?= $p['stock'] ?> units</span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><?= $p['stock'] ?> units</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['is_active']): ?>
                                    <span class="badge bg-success small">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary small">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="/admin/products/<?= $p['id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="/admin/products/<?= $p['id'] ?>/delete" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete or deactivate product <?= e($p['name']) ?>?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white p-3 border-top d-flex justify-content-between align-items-center">
            <span class="small text-muted">Showing page <?= $page ?> of <?= $totalPages ?> (<?= $totalItems ?> total items)</span>
            <ul class="pagination pagination-sm m-0">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/products?page=<?= $page - 1 ?>&q=<?= urlencode($filters['q'] ?? '') ?>">Previous</a>
                </li>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/products?page=<?= $p ?>&q=<?= urlencode($filters['q'] ?? '') ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/products?page=<?= $page + 1 ?>&q=<?= urlencode($filters['q'] ?? '') ?>">Next</a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
</div>
