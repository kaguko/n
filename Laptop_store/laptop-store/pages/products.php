<?php
/**
 * Products Page - Trang danh sách sản phẩm
 */
$page_title = 'Danh sách sản phẩm';

// Lấy filter từ URL
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$brand_id = isset($_GET['brand']) ? intval($_GET['brand']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;
$ram = isset($_GET['ram']) ? $_GET['ram'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Pagination
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build query
$where = ["p.status = 'active'"];
$params = [];

if ($category_id > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
}

if ($brand_id > 0) {
    $where[] = "p.brand_id = ?";
    $params[] = $brand_id;
}

if (!empty($search)) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($min_price > 0) {
    $where[] = "COALESCE(p.discount_price, p.price) >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $where[] = "COALESCE(p.discount_price, p.price) <= ?";
    $params[] = $max_price;
}

if (!empty($ram)) {
    $where[] = "p.ram LIKE ?";
    $params[] = "%$ram%";
}

$where_clause = implode(' AND ', $where);

// Sorting
$order_by = match($sort) {
    'price_asc' => 'COALESCE(p.discount_price, p.price) ASC',
    'price_desc' => 'COALESCE(p.discount_price, p.price) DESC',
    'name' => 'p.name ASC',
    'popular' => 'p.views DESC',
    default => 'p.created_at DESC'
};

// Count total
$count_sql = "SELECT COUNT(*) as total FROM products p WHERE $where_clause";
$total_result = db_fetch_one($count_sql, $params);
$total = $total_result['total'];

// Get products
$sql = "
    SELECT p.*, c.name as category_name, b.name as brand_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    WHERE $where_clause
    ORDER BY $order_by
    LIMIT $per_page OFFSET $offset
";

$products = db_fetch_all($sql, $params);

// Get filters data
$categories = db_fetch_all("SELECT * FROM categories ORDER BY name") ?? [];
$brands = db_fetch_all("SELECT * FROM brands ORDER BY name") ?? [];

// Pagination
$pagination = paginate($total, $per_page, $page);

// Build filter query string
function build_filter_query($page_num = null, $exclude = []) {
    global $category_id, $brand_id, $search, $min_price, $max_price, $ram, $sort, $page;
    
    $params = ['page' => 'products'];
    
    if ($page_num !== null) {
        $params['p'] = $page_num;
    }
    
    if ($category_id && !in_array('category', $exclude)) $params['category'] = $category_id;
    if ($brand_id && !in_array('brand', $exclude)) $params['brand'] = $brand_id;
    if ($search && !in_array('search', $exclude)) $params['search'] = $search;
    if ($min_price && !in_array('min_price', $exclude)) $params['min_price'] = $min_price;
    if ($max_price && !in_array('max_price', $exclude)) $params['max_price'] = $max_price;
    if ($ram && !in_array('ram', $exclude)) $params['ram'] = $ram;
    if ($sort && $sort != 'newest' && !in_array('sort', $exclude)) $params['sort'] = $sort;
    
    return '?' . http_build_query($params);
}
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-funnel"></i> Bộ lọc
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo base_url('index.php'); ?>">
                        <input type="hidden" name="page" value="products">
                        
                        <!-- Search -->
                        <?php if (!empty($search)): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tìm kiếm</label>
                            <input type="text" class="form-control" name="search" value="<?php echo e($search); ?>">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select class="form-select" name="category">
                                <option value="">Tất cả</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Brand -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hãng</label>
                            <select class="form-select" name="brand">
                                <option value="">Tất cả</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>" <?php echo $brand_id == $brand['id'] ? 'selected' : ''; ?>>
                                        <?php echo e($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Khoảng giá</label>
                            <div class="input-group mb-2">
                                <input type="number" class="form-control" name="min_price" placeholder="Từ" value="<?php echo $min_price > 0 ? $min_price : ''; ?>">
                                <span class="input-group-text">-</span>
                                <input type="number" class="form-control" name="max_price" placeholder="Đến" value="<?php echo $max_price > 0 ? $max_price : ''; ?>">
                            </div>
                            <small class="text-muted">Đơn vị: VNĐ</small>
                        </div>
                        
                        <!-- RAM -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">RAM</label>
                            <select class="form-select" name="ram">
                                <option value="">Tất cả</option>
                                <option value="8GB" <?php echo $ram == '8GB' ? 'selected' : ''; ?>>8GB</option>
                                <option value="16GB" <?php echo $ram == '16GB' ? 'selected' : ''; ?>>16GB</option>
                                <option value="32GB" <?php echo $ram == '32GB' ? 'selected' : ''; ?>>32GB</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-search"></i> Áp dụng
                        </button>
                        
                        <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Xóa bộ lọc
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="col-md-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0">
                        <?php if (!empty($search)): ?>
                            Kết quả tìm kiếm: "<?php echo e($search); ?>"
                        <?php elseif ($category_id > 0): ?>
                            <?php
                            $current_cat = array_filter($categories, fn($c) => $c['id'] == $category_id);
                            $current_cat = reset($current_cat);
                            echo e($current_cat['name']);
                            ?>
                        <?php else: ?>
                            Tất cả sản phẩm
                        <?php endif; ?>
                    </h4>
                    <small class="text-muted">Tìm thấy <?php echo $total; ?> sản phẩm</small>
                </div>
                
                <form method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="page" value="products">
                    <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                    <?php if ($brand_id): ?><input type="hidden" name="brand" value="<?php echo $brand_id; ?>"><?php endif; ?>
                    <?php if ($search): ?><input type="hidden" name="search" value="<?php echo e($search); ?>"><?php endif; ?>
                    <?php if ($min_price): ?><input type="hidden" name="min_price" value="<?php echo $min_price; ?>"><?php endif; ?>
                    <?php if ($max_price): ?><input type="hidden" name="max_price" value="<?php echo $max_price; ?>"><?php endif; ?>
                    <?php if ($ram): ?><input type="hidden" name="ram" value="<?php echo e($ram); ?>"><?php endif; ?>
                    
                    <label class="me-2">Sắp xếp:</label>
                    <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="popular" <?php echo $sort == 'popular' ? 'selected' : ''; ?>>Phổ biến</option>
                        <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá thấp → cao</option>
                        <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá cao → thấp</option>
                        <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A → Z</option>
                    </select>
                </form>
            </div>

            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy sản phẩm nào phù hợp.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">
                            <?php if ($product['discount_price']): ?>
                                <span class="badge bg-danger badge-featured">
                                    -<?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>%
                                </span>
                            <?php endif; ?>
                            
                            <img src="<?php echo get_image_url($product['image']); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo e($product['name']); ?>">
                            
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

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo build_filter_query($page - 1); ?>">
                                    Trước
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == 1 || $i == $pagination['total_pages'] || abs($i - $page) <= 2): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo build_filter_query($i); ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php elseif (abs($i - $page) == 3): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo build_filter_query($page + 1); ?>">
                                    Sau
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
