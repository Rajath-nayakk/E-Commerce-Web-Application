<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h4 class="fw-bold m-0">Edit Product: <?= e($product['name']) ?></h4>
                    <span class="small text-muted font-monospace">SKU: <?= e($product['slug']) ?></span>
                </div>
                <a href="/admin/products" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Back to Products
                </a>
            </div>

            <form action="/admin/products/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row g-3">
                    <!-- Product Name -->
                    <div class="col-md-8">
                        <label for="name" class="form-label small fw-semibold text-muted">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e(old('name', $product['name'])) ?>" required>
                    </div>

                    <!-- Category -->
                    <div class="col-md-4">
                        <label for="category_id" class="form-label small fw-semibold text-muted">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="col-md-4">
                        <label for="price" class="form-label small fw-semibold text-muted">Unit Price ($)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="price" name="price" value="<?= e(old('price', $product['price'])) ?>" required>
                        </div>
                    </div>

                    <!-- Stock Quantity -->
                    <div class="col-md-4">
                        <label for="stock" class="form-label small fw-semibold text-muted">Stock Quantity</label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="<?= e(old('stock', $product['stock'])) ?>" required>
                    </div>

                    <!-- Active Status -->
                    <div class="col-md-4">
                        <label for="is_active" class="form-label small fw-semibold text-muted">Publish Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" <?= ($product['is_active'] == 1) ? 'selected' : '' ?>>Active (Visible)</option>
                            <option value="0" <?= ($product['is_active'] == 0) ? 'selected' : '' ?>>Draft (Hidden)</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label small fw-semibold text-muted">Full Description & Specifications</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?= e(old('description', $product['description'])) ?></textarea>
                    </div>

                    <!-- Current & New Image Upload -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted d-block">Current Product Image</label>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="<?= upload_url($product['image']) ?>" alt="Current image" class="img-thumbnail rounded bg-dark" style="max-height: 100px;">
                            <span class="small text-muted font-monospace"><?= e($product['image']) ?></span>
                        </div>

                        <label for="productImageInput" class="form-label small fw-semibold text-muted">Upload New Image (Optional)</label>
                        <input class="form-control" type="file" id="productImageInput" name="image" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                        <div class="mt-3" id="imagePreviewContainer"></div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="/admin/products" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                            <i class="bi bi-check-lg me-1"></i>Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

