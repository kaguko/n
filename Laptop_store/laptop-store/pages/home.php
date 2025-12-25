<?php
/**
 * Home Page - Trang chủ
 */
$page_title = 'Trang chủ';

// Lấy sản phẩm nổi bật
$featured_products = db_fetch_all("
    SELECT p.*, c.name as category_name, b.name as brand_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.is_featured = 1 AND p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 8
");

// Lấy sản phẩm mới nhất
$latest_products = db_fetch_all("
    SELECT p.*, c.name as category_name, b.name as brand_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 8
");
?>

<!-- Hero Slider -->
<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="text-white py-5" style="height: 500px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
                <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1440 320%22><path fill=%22%23ffffff%22 fill-opacity=%220.1%22 d=%22M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z%22></path></svg>') no-repeat bottom; background-size: cover; opacity: 0.3;"></div>
                <div class="container h-100 d-flex align-items-center position-relative" style="z-index: 2;">
                    <div class="row w-100">
                        <div class="col-md-7 hero-section">
                            <div class="badge bg-white text-primary mb-3 px-3 py-2" style="font-size: 0.9rem;">🔥 HOT SALE</div>
                            <h1 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Laptop Gaming<br>Đỉnh Cao</h1>
                            <p class="lead mb-4 fs-5">Trải nghiệm gaming mượt mà với RTX 4000 series<br>và CPU thế hệ mới nhất. Giảm giá lên đến 25%!</p>
                            <a href="<?php echo base_url('index.php?page=products&category=1'); ?>" class="btn btn-light btn-lg shadow-lg">
                                <i class="bi bi-rocket-takeoff"></i> Khám phá ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="carousel-item">
            <div class="text-white py-5" style="height: 500px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); position: relative; overflow: hidden;">
                <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1440 320%22><path fill=%22%23ffffff%22 fill-opacity=%220.1%22 d=%22M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,90.7C672,85,768,107,864,122.7C960,139,1056,149,1152,138.7C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z%22></path></svg>') no-repeat bottom; background-size: cover; opacity: 0.3;"></div>
                <div class="container h-100 d-flex align-items-center position-relative" style="z-index: 2;">
                    <div class="row w-100">
                        <div class="col-md-7 hero-section">
                            <div class="badge bg-white text-success mb-3 px-3 py-2" style="font-size: 0.9rem;">💼 CHO DOANH NHÂN</div>
                            <h1 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Laptop<br>Văn Phòng</h1>
                            <p class="lead mb-4 fs-5">Hiệu suất vượt trội, pin trâu cho cả ngày làm việc.<br>Thiết kế mỏng nhẹ, sang trọng.</p>
                            <a href="<?php echo base_url('index.php?page=products&category=2'); ?>" class="btn btn-light btn-lg shadow-lg">
                                <i class="bi bi-briefcase"></i> Xem ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="carousel-item">
            <div class="text-white py-5" style="height: 500px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); position: relative; overflow: hidden;">
                <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1440 320%22><path fill=%22%23ffffff%22 fill-opacity=%220.1%22 d=%22M0,192L48,197.3C96,203,192,213,288,192C384,171,480,117,576,112C672,107,768,149,864,154.7C960,160,1056,128,1152,101.3C1248,75,1344,53,1392,42.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z%22></path></svg>') no-repeat bottom; background-size: cover; opacity: 0.3;"></div>
                <div class="container h-100 d-flex align-items-center position-relative" style="z-index: 2;">
                    <div class="row w-100">
                        <div class="col-md-7 hero-section">
                            <div class="badge bg-white text-danger mb-3 px-3 py-2" style="font-size: 0.9rem;">⚡ FLASH SALE</div>
                            <h1 class="display-3 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Giảm Giá<br>Sốc!</h1>
                            <p class="lead mb-4 fs-5">Ưu đãi lên đến 30% cho các sản phẩm chọn lọc.<br>Số lượng có hạn, nhanh tay kẻo lỡ!</p>
                            <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-dark btn-lg shadow-lg">
                                <i class="bi bi-lightning-charge"></i> Mua ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<div class="container">
    <!-- Features -->
    <div class="row mb-5 g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4" style="border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-truck display-5 text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">Giao hàng miễn phí</h5>
                <p class="text-muted mb-0">Với đơn hàng trên 10 triệu</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4" style="border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="bi bi-shield-check display-5 text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">Bảo hành chính hãng</h5>
                <p class="text-muted mb-0">100% sản phẩm chính hãng</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4" style="border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-arrow-repeat display-5 text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">Đổi trả dễ dàng</h5>
                <p class="text-muted mb-0">Trong vòng 7 ngày</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-4" style="border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-headset display-5 text-white"></i>
                </div>
                <h5 class="fw-bold mb-2">Hỗ trợ 24/7</h5>
                <p class="text-muted mb-0">Hotline: 1900 xxxx</p>
            </div>
        </div>
    </div>

    <!-- Sản phẩm nổi bật -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold section-title">
                <i class="bi bi-star-fill text-warning"></i> Sản phẩm nổi bật
            </h2>
            <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-outline-primary">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <?php if ($product['discount_price']): ?>
                        <span class="badge bg-danger badge-featured">
                            -<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%
                        </span>
                    <?php endif; ?>
                    
                    <img src="<?php echo get_image_url($product['image']); ?>" 
                         class="card-img-top product-image" 
                         alt="<?php echo e($product['name']); ?>"
>
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo e($product['name']); ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag"></i> <?php echo e($product['brand_name']); ?>
                        </p>
                        
                        <div class="mt-auto">
                            <?php if ($product['discount_price']): ?>
                                <div class="old-price"><?php echo format_price($product['price']); ?></div>
                                <div class="price"><?php echo format_price($product['discount_price']); ?></div>
                            <?php else: ?>
                                <div class="price"><?php echo format_price($product['price']); ?></div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 mt-2">
                                <a href="<?php echo base_url('index.php?page=product-detail&id=' . $product['id']); ?>" 
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                                <a href="<?php echo base_url('index.php?page=add-to-cart&id=' . $product['id']); ?>" 
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
    </section>

    <!-- Danh mục nổi bật -->
    <section class="mb-5">
        <h2 class="fw-bold mb-4 section-title">
            <i class="bi bi-grid-3x3-gap"></i> Danh mục sản phẩm
        </h2>
        
        <div class="row g-4">
            <?php
            $categories_home = db_fetch_all("SELECT * FROM categories LIMIT 6");
            $category_gradients = [
                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
                'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'linear-gradient(135deg, #30cfd0 0%, #330867 100%)'
            ];
            ?>
            
            <?php foreach ($categories_home as $index => $category): ?>
            <div class="col-md-4">
                <a href="<?php echo base_url('index.php?page=products&category=' . $category['id']); ?>" 
                   class="text-decoration-none">
                    <div class="card category-card text-white border-0" style="background: <?php echo $category_gradients[$index % 6]; ?>; min-height: 180px;">
                        <div class="card-body text-center py-5 position-relative">
                            <i class="bi bi-laptop display-3 mb-3"></i>
                            <h4 class="fw-bold mb-2"><?php echo e($category['name']); ?></h4>
                            <p class="mb-0 opacity-75"><?php echo e($category['description']); ?></p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sản phẩm mới nhất -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold section-title">
                <i class="bi bi-clock-history text-primary"></i> Sản phẩm mới nhất
            </h2>
            <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-outline-primary">
                Xem tất cả <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <div class="row">
            <?php foreach ($latest_products as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <?php if ($product['discount_price']): ?>
                        <span class="badge bg-danger badge-featured">
                            -<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%
                        </span>
                    <?php endif; ?>
                    
                    <img src="<?php echo get_image_url($product['image']); ?>" 
                         class="card-img-top product-image" 
                         alt="<?php echo e($product['name']); ?>"
>
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title"><?php echo e($product['name']); ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-tag"></i> <?php echo e($product['brand_name']); ?>
                        </p>
                        
                        <div class="mt-auto">
                            <?php if ($product['discount_price']): ?>
                                <div class="old-price"><?php echo format_price($product['price']); ?></div>
                                <div class="price"><?php echo format_price($product['discount_price']); ?></div>
                            <?php else: ?>
                                <div class="price"><?php echo format_price($product['price']); ?></div>
                            <?php endif; ?>
                            
                            <div class="d-flex gap-2 mt-2">
                                <a href="<?php echo base_url('index.php?page=product-detail&id=' . $product['id']); ?>" 
                                   class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                                <a href="<?php echo base_url('index.php?page=add-to-cart&id=' . $product['id']); ?>" 
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
    </section>
</div>
