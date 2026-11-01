<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Catalog</li>
            <?php if ($currentCategory): ?>
                <li class="breadcrumb-item active" aria-current="page"><?= e($currentCategory['name']) ?></li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Page Header & Active Filters Overview -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">
                <?= $currentCategory ? e($currentCategory['name']) : 'Explore Tech Hardware' ?>
            </h1>
            <p class="text-muted small m-0">
                Showing <?= count($products) ?> of <?= $totalItems ?> total hardware items
                <?php if (!empty($filters['q'])): ?>
                    matching &ldquo;<span class="fw-bold text-dark"><?= e($filters['q']) ?></span>&rdquo;
                <?php endif; ?>
            </p>
        </div>

        <!-- Sorting and Filters bar -->
        <form action="/products" method="GET" class="d-flex align-items-center gap-2">
            <?php if (!empty($filters['category'])): ?>
                <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
            <?php endif; ?>
            <?php if (!empty($filters['q'])): ?>
                <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
            <?php endif; ?>

            <label for="sort" class="small fw-semibold text-muted text-nowrap">Sort By:</label>
            <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="name_asc" <?= ($filters['sort'] === 'name_asc') ? 'selected' : '' ?>>Name: A to Z</option>
            </select>
        </form>
    </div>

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 mb-4">
                <h6 class="fw-bold text-uppercase small text-muted mb-3">Filter Catalog</h6>
                <form action="/products" method="GET">
                    <!-- Search Field -->
                    <div class="mb-3">
                        <label for="searchFilter" class="form-label small fw-semibold text-muted">Keyword</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="searchFilter" name="q" value="<?= e($filters['q']) ?>" placeholder="Search...">
                            <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Department</label>
                        <div class="list-group list-group-flush small">
                            <a href="/products<?= !empty($filters['q']) ? '?q=' . urlencode($filters['q']) : '' ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1 border-0 <?= empty($filters['category']) ? 'fw-bold text-primary' : 'text-muted' ?>">
                                <span>All Departments</span>
                                <span class="badge bg-light text-dark rounded-pill"><?= $totalItems ?></span>
                            </a>
                            <?php foreach ($categories as $cat): 
                                $isActive = ($filters['category'] === $cat['slug']);
                                $catUrl = '/products?category=' . urlencode($cat['slug']);
                                if (!empty($filters['q'])) $catUrl .= '&q=' . urlencode($filters['q']);
                            ?>
                                <a href="<?= $catUrl ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1 border-0 <?= $isActive ? 'fw-bold text-primary' : 'text-muted' ?>">
                                    <span><?= e($cat['name']) ?></span>
                                    <span class="badge bg-light text-dark rounded-pill"><?= $cat['product_count'] ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStockCheck" <?= !empty($filters['in_stock']) ? 'checked' : '' ?> onchange="this.form.submit()">
                        <label class="form-check-label small fw-semibold" for="inStockCheck">In Stock Only</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                        <?php if (!empty($filters['q']) || !empty($filters['category']) || !empty($filters['in_stock'])): ?>
                            <a href="/products" class="btn btn-outline-secondary btn-sm">Reset All</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <!-- Empty State -->
                <div class="card p-5 text-center border-0 shadow-sm rounded-4">
                    <div class="display-3 text-muted mb-3"><i class="bi bi-search"></i></div>
                    <h3 class="h4 fw-bold">No Products Found</h3>
                    <p class="text-muted small mb-4">
                        We could not find any products matching your current search or category criteria.
                    </p>
                    <div>
                        <a href="/products" class="btn btn-primary px-4 rounded-pill">
                            Clear Filters & View All
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col">
                            <div class="product-card">
                                <div class="product-image-wrap">
                                    <span class="product-badge-cat"><?= e($product['category_name']) ?></span>
                                    <?php if ($product['stock'] > 0): ?>
                                        <span class="product-badge-stock badge bg-success"><?= $product['stock'] ?> in stock</span>
                                    <?php else: ?>
                                        <span class="product-badge-stock badge bg-danger">Out of stock</span>
                                    <?php endif; ?>
                                    <a href="/products/<?= e($product['slug']) ?>">
                                        <img src="<?= upload_url($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                                    </a>
                                </div>
                                <div class="product-body">
                                    <h3 class="product-title">
                                        <a href="/products/<?= e($product['slug']) ?>"><?= e($product['name']) ?></a>
                                    </h3>
                                    <p class="product-desc-clamp"><?= e($product['description']) ?></p>
                                    <div class="product-footer">
                                        <span class="product-price"><?= format_price($product['price']) ?></span>
                                        <?php if ($product['stock'] > 0): ?>
                                            <form action="/cart/add" method="POST" class="ajax-add-cart m-0">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn-add-cart">
                                                    <i class="bi bi-cart-plus me-1"></i>Add
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Sold Out</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Controls -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Page Link -->
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <?php
                                $prevQuery = array_merge($filters, ['page' => $currentPage - 1]);
                                ?>
                                <a class="page-link" href="/products?<?= http_build_query($prevQuery) ?>" aria-label="Previous">
                                    &laquo; Previous
                                </a>
                            </li>

                            <!-- Page Numbers -->
                            <?php for ($p = 1; $p <= $totalPages; $p++): 
                                $pageQuery = array_merge($filters, ['page' => $p]);
                            ?>
                                <li class="page-item <?= ($p === $currentPage) ? 'active' : '' ?>">
                                    <a class="page-link" href="/products?<?= http_build_query($pageQuery) ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Next Page Link -->
                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <?php
                                $nextQuery = array_merge($filters, ['page' => $currentPage + 1]);
                                ?>
                                <a class="page-link" href="/products?<?= http_build_query($nextQuery) ?>" aria-label="Next">
                                    Next &raquo;
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
