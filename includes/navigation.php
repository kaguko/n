<?php
/**
 * Navigation - Menu điều hướng
 */
$cart_count = cart_count();
$categories = db_fetch_all("SELECT * FROM categories ORDER BY name") ?? [];
$brands = db_fetch_all("SELECT * FROM brands ORDER BY name") ?? [];
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 15px rgba(0,0,0,0.1);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo base_url('index.php'); ?>" style="font-size: 1.5rem;">
            <i class="bi bi-laptop"></i> Laptop Store
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('index.php'); ?>">Trang chủ</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Danh mục
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo base_url('index.php?page=products'); ?>">Tất cả sản phẩm</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo base_url('index.php?page=products&category=' . $cat['id']); ?>">
                                    <?php echo e($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Hãng
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach ($brands as $brand): ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo base_url('index.php?page=products&brand=' . $brand['id']); ?>">
                                    <?php echo e($brand['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
            
            <!-- Search Form -->
            <form class="d-flex me-3" method="GET" action="<?php echo base_url('index.php'); ?>">
                <input type="hidden" name="page" value="products">
                <input class="form-control me-2" type="search" name="search" placeholder="Tìm laptop..." value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
                <button class="btn btn-outline-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            
            <!-- User Menu -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="<?php echo base_url('index.php?page=cart'); ?>">
                        <i class="bi bi-cart3"></i> Giỏ hàng
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo e($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (is_admin()): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo base_url('admin/index.php'); ?>">
                                        <i class="bi bi-speedometer2"></i> Admin Panel
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="<?php echo base_url('index.php?page=orders'); ?>">
                                    <i class="bi bi-bag-check"></i> Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo base_url('index.php?page=logout'); ?>">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('index.php?page=login'); ?>">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo base_url('index.php?page=register'); ?>">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
