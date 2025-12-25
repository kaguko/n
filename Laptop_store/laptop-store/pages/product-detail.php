<?php
/**
 * Product Detail Page - Trang chi tiết sản phẩm
 */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(base_url('index.php?page=products'));
}

$product_id = intval($_GET['id']);

// Lấy thông tin sản phẩm
$product = db_fetch_one("
    SELECT p.*, c.name as category_name, b.name as brand_name, c.id as category_id
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.id = ? AND p.status = 'active'
", [$product_id]);

if (!$product) {
    redirect(base_url('index.php?page=products'));
}

// Cập nhật lượt xem
db_query("UPDATE products SET views = views + 1 WHERE id = ?", [$product_id]);

// Lấy sản phẩm liên quan
$related_products = db_fetch_all("
    SELECT p.*, b.name as brand_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
    ORDER BY RAND()
    LIMIT 4
", [$product['category_id'], $product_id]);

$page_title = $product['name'];
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php?page=products'); ?>">Sản phẩm</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php?page=products&category=' . $product['category_id']); ?>"><?php echo e($product['category_name']); ?></a></li>
            <li class="breadcrumb-item active"><?php echo e($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="card">
                <img src="<?php echo get_image_url($product['image']); ?>" 
                     class="card-img-top" 
                     alt="<?php echo e($product['name']); ?>">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <h2 class="mb-3"><?php echo e($product['name']); ?></h2>
            
            <div class="mb-3">
                <span class="badge bg-primary"><?php echo e($product['category_name']); ?></span>
                <span class="badge bg-secondary"><?php echo e($product['brand_name']); ?></span>
                <?php if ($product['is_featured']): ?>
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-star-fill"></i> Nổi bật
                    </span>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <small class="text-muted">
                    <i class="bi bi-eye"></i> <?php echo number_format($product['views']); ?> lượt xem
                </small>
            </div>

            <div class="mb-4">
                <?php if ($product['discount_price']): ?>
                    <h3 class="price mb-2"><?php echo format_price($product['discount_price']); ?></h3>
                    <div class="old-price h5"><?php echo format_price($product['price']); ?></div>
                    <span class="badge bg-danger">
                        Tiết kiệm: <?php echo format_price($product['price'] - $product['discount_price']); ?> 
                        (-<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%)
                    </span>
                <?php else: ?>
                    <h3 class="price"><?php echo format_price($product['price']); ?></h3>
                <?php endif; ?>
            </div>

            <!-- Add to cart form -->
            <form action="<?php echo base_url('index.php?page=add-to-cart'); ?>" method="POST" class="mb-4">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        <label class="col-form-label">Số lượng:</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" class="form-control" name="quantity" value="1" min="1" max="10" style="width: 100px;">
                    </div>
                    <div class="col-auto">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="text-success">
                                <i class="bi bi-check-circle"></i> Còn hàng (<?php echo $product['stock']; ?>)
                            </span>
                        <?php else: ?>
                            <span class="text-danger">
                                <i class="bi bi-x-circle"></i> Hết hàng
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($product['stock'] > 0): ?>
                    <div class="d-grid gap-2 d-md-block">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                        <button type="submit" name="buy_now" value="1" class="btn btn-success btn-lg">
                            <i class="bi bi-lightning-charge"></i> Mua ngay
                        </button>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-x-circle"></i> Tạm hết hàng
                    </button>
                <?php endif; ?>
            </form>

            <!-- Product highlights -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin nổi bật</h5>
                    <ul class="list-unstyled mb-0">
                        <?php if ($product['cpu']): ?>
                            <li class="mb-2">
                                <i class="bi bi-cpu text-primary"></i> 
                                <strong>CPU:</strong> <?php echo e($product['cpu']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['ram']): ?>
                            <li class="mb-2">
                                <i class="bi bi-memory text-success"></i> 
                                <strong>RAM:</strong> <?php echo e($product['ram']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['storage']): ?>
                            <li class="mb-2">
                                <i class="bi bi-device-ssd text-info"></i> 
                                <strong>Ổ cứng:</strong> <?php echo e($product['storage']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['screen']): ?>
                            <li class="mb-2">
                                <i class="bi bi-display text-warning"></i> 
                                <strong>Màn hình:</strong> <?php echo e($product['screen']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['graphics']): ?>
                            <li class="mb-2">
                                <i class="bi bi-gpu-card text-danger"></i> 
                                <strong>Card đồ họa:</strong> <?php echo e($product['graphics']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['os']): ?>
                            <li class="mb-2">
                                <i class="bi bi-windows text-primary"></i> 
                                <strong>Hệ điều hành:</strong> <?php echo e($product['os']); ?>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($product['weight']): ?>
                            <li class="mb-2">
                                <i class="bi bi-box text-secondary"></i> 
                                <strong>Trọng lượng:</strong> <?php echo e($product['weight']); ?>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tabs -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#description">
                        <i class="bi bi-file-text"></i> Mô tả
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#specifications">
                        <i class="bi bi-list-check"></i> Thông số kỹ thuật
                    </a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-4">
                <div id="description" class="tab-pane fade show active">
                    <h4>Mô tả sản phẩm</h4>
                    <div class="text-justify">
                        <?php echo nl2br(e($product['description'])); ?>
                    </div>
                </div>

                <div id="specifications" class="tab-pane fade">
                    <h4>Thông số kỹ thuật chi tiết</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Tên sản phẩm</th>
                                <td><?php echo e($product['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Thương hiệu</th>
                                <td><?php echo e($product['brand_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Danh mục</th>
                                <td><?php echo e($product['category_name']); ?></td>
                            </tr>
                            <?php if ($product['cpu']): ?>
                            <tr>
                                <th>Bộ xử lý (CPU)</th>
                                <td><?php echo e($product['cpu']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['ram']): ?>
                            <tr>
                                <th>RAM</th>
                                <td><?php echo e($product['ram']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['storage']): ?>
                            <tr>
                                <th>Ổ cứng</th>
                                <td><?php echo e($product['storage']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['screen']): ?>
                            <tr>
                                <th>Màn hình</th>
                                <td><?php echo e($product['screen']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['graphics']): ?>
                            <tr>
                                <th>Card đồ họa</th>
                                <td><?php echo e($product['graphics']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['os']): ?>
                            <tr>
                                <th>Hệ điều hành</th>
                                <td><?php echo e($product['os']); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($product['weight']): ?>
                            <tr>
                                <th>Trọng lượng</th>
                                <td><?php echo e($product['weight']); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
        </div>
        
        <?php foreach ($related_products as $rproduct): ?>
        <div class="col-md-3 mb-4">
            <div class="card product-card h-100">
                <?php if ($rproduct['discount_price']): ?>
                    <span class="badge bg-danger badge-featured">
                        -<?php echo round((($rproduct['price'] - $rproduct['discount_price']) / $rproduct['price']) * 100); ?>%
                    </span>
                <?php endif; ?>
                
                <img src="<?php echo get_image_url($rproduct['image']); ?>" 
                     class="card-img-top product-image" 
                     alt="<?php echo e($rproduct['name']); ?>"
                     onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title"><?php echo e($rproduct['name']); ?></h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-tag"></i> <?php echo e($rproduct['brand_name']); ?>
                    </p>
                    
                    <div class="mt-auto">
                        <?php if ($rproduct['discount_price']): ?>
                            <div class="old-price"><?php echo format_price($rproduct['price']); ?></div>
                            <div class="price"><?php echo format_price($rproduct['discount_price']); ?></div>
                        <?php else: ?>
                            <div class="price"><?php echo format_price($rproduct['price']); ?></div>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2 mt-2">
                            <a href="<?php echo base_url('index.php?page=product-detail&id=' . $rproduct['id']); ?>" 
                               class="btn btn-outline-primary btn-sm flex-grow-1">
                                <i class="bi bi-eye"></i> Chi tiết
                            </a>
                            <a href="<?php echo base_url('index.php?page=add-to-cart&id=' . $rproduct['id']); ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="bi bi-cart-plus"></i>
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
