<div class="row g-4">
    <!-- Categories List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h5 class="fw-bold m-0">Hardware Categories (<?= count($categories) ?> total)</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-muted text-uppercase">
                        <tr>
                            <th style="width: 60px;">#ID</th>
                            <th>Category Name</th>
                            <th>Slug</th>
                            <th class="text-center">Products</th>
                            <th class="text-end" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No categories created yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td class="text-muted fw-bold">#<?= $cat['id'] ?></td>
                                    <td>
                                        <span class="fw-bold text-dark"><?= e($cat['name']) ?></span>
                                    </td>
                                    <td>
                                        <span class="small font-monospace text-muted"><?= e($cat['slug']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border rounded-pill">
                                            <?= $cat['product_count'] ?> items
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete category <?= e($cat['name']) ?>?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete" <?= ($cat['product_count'] > 0) ? 'disabled' : '' ?>>
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Category Modal -->
                                        <div class="modal fade" id="editModal<?= $cat['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content rounded-4 border-0 shadow">
                                                    <form action="/admin/categories/<?= $cat['id'] ?>" method="POST">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title fw-bold">Edit Category #<?= $cat['id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <label for="cat_name_<?= $cat['id'] ?>" class="form-label small fw-semibold text-muted">Category Name</label>
                                                            <input type="text" class="form-control" id="cat_name_<?= $cat['id'] ?>" name="name" value="<?= e($cat['name']) ?>" required>
                                                        </div>
                                                        <div class="modal-footer border-top">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary fw-semibold">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Category Form Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 85px;">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle text-primary"></i>
                <span>Add Category</span>
            </h5>
            <form action="/admin/categories" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="new_cat_name" class="form-label small fw-semibold text-muted">Category Name</label>
                    <input type="text" class="form-control" id="new_cat_name" name="name" placeholder="e.g. Virtual Reality & AR" required autofocus>
                    <div class="form-text small text-muted">A URL slug will be auto-generated automatically.</div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
