<?php
/**
 * Admin Products - Quản lý sản phẩm
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Quản lý sản phẩm';

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM products WHERE id = ?", [$id]);
    set_flash('success', 'Đã xóa sản phẩm!');
    redirect('products.php');
}

// Xử lý thêm/sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $brand_id = intval($_POST['brand_id']);
    $price = floatval($_POST['price']);
    $discount_price = !empty($_POST['discount_price']) ? floatval($_POST['discount_price']) : null;
    $cpu = trim($_POST['cpu']);
    $ram = trim($_POST['ram']);
    $storage = trim($_POST['storage']);
    $screen = trim($_POST['screen']);
    $graphics = trim($_POST['graphics']);
    $os = trim($_POST['os']);
    $weight = trim($_POST['weight']);
    $description = trim($_POST['description']);
    $stock = intval($_POST['stock']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'];
    
    $slug = create_slug($name);
    
    // Lấy URL ảnh
    $image = trim($_POST['image']);
    
    if ($id > 0) {
        // Update
        $sql = "UPDATE products SET name=?, slug=?, category_id=?, brand_id=?, price=?, discount_price=?, 
                cpu=?, ram=?, storage=?, screen=?, graphics=?, os=?, weight=?, description=?, 
                stock=?, is_featured=?, status=?";
        $params = [$name, $slug, $category_id, $brand_id, $price, $discount_price, $cpu, $ram, 
                   $storage, $screen, $graphics, $os, $weight, $description, $stock, $is_featured, $status];
        
        if ($image) {
            $sql .= ", image=?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id=?";
        $params[] = $id;
        
        db_query($sql, $params);
        set_flash('success', 'Đã cập nhật sản phẩm!');
    } else {
        // Insert
        $product_id = db_insert("
            INSERT INTO products (name, slug, category_id, brand_id, price, discount_price, cpu, ram, storage, 
                                  screen, graphics, os, weight, image, description, stock, is_featured, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [$name, $slug, $category_id, $brand_id, $price, $discount_price, $cpu, $ram, $storage, 
            $screen, $graphics, $os, $weight, $image, $description, $stock, $is_featured, $status]);
        
        set_flash('success', 'Đã thêm sản phẩm mới!');
    }
    
    redirect('products.php');
}

// Lấy sản phẩm để edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_product = db_fetch_one("SELECT * FROM products WHERE id = ?", [$edit_id]);
}

// Lấy danh sách sản phẩm
$products = db_fetch_all("
    SELECT p.*, c.name as category_name, b.name as brand_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN brands b ON p.brand_id = b.id
    ORDER BY p.created_at DESC
");

$categories = db_fetch_all("SELECT * FROM categories ORDER BY name");
$brands = db_fetch_all("SELECT * FROM brands ORDER BY name");

include 'includes/header.php';
?>

<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetForm()">
        <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
    </button>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Hãng</th>
                        <th>Giá</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <img src="<?php echo get_image_url($product['image']); ?>" 
                                 width="50" class="img-thumbnail"
                                 onerror="this.src='https://via.placeholder.com/50'">
                        </td>
                        <td>
                            <?php echo e($product['name']); ?>
                            <?php if ($product['is_featured']): ?>
                                <span class="badge bg-warning">Nổi bật</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($product['category_name']); ?></td>
                        <td><?php echo e($product['brand_name']); ?></td>
                        <td>
                            <?php if ($product['discount_price']): ?>
                                <s class="text-muted small"><?php echo format_price($product['price']); ?></s><br>
                                <span class="text-danger fw-bold"><?php echo format_price($product['discount_price']); ?></span>
                            <?php else: ?>
                                <span class="fw-bold"><?php echo format_price($product['price']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <?php if ($product['status'] == 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" onclick='editProduct(<?php echo json_encode($product); ?>)'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bạn có chắc muốn xóa?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kho *</label>
                            <input type="number" class="form-control" name="stock" value="0" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>">
                                        <?php echo e($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hãng *</label>
                            <select class="form-select" name="brand_id" required>
                                <option value="">-- Chọn hãng --</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>">
                                        <?php echo e($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá gốc *</label>
                            <input type="number" class="form-control" name="price" 
                                   value=""" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <input type="number" class="form-control" name="discount_price" 
                                   value=""">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CPU</label>
                            <input type="text" class="form-control" name="cpu" 
                                   value=""">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RAM</label>
                            <input type="text" class="form-control" name="ram" 
                                   value=""">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ổ cứng</label>
                            <input type="text" class="form-control" name="storage" 
                                   value=""">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Màn hình</label>
                            <input type="text" class="form-control" name="screen" 
                                   value=""">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Card đồ họa</label>
                            <input type="text" class="form-control" name="graphics" 
                                   value=""">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hệ điều hành</label>
                            <input type="text" class="form-control" name="os" 
                                   value=""">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trọng lượng</label>
                            <input type="text" class="form-control" name="weight" 
                                   value=""">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">URL Ảnh sản phẩm</label>
                        <input type="url" class="form-control" name="image" 
                               placeholder="https://example.com/laptop.jpg"
                               value=""">
                        <small class="text-muted">Nhập URL ảnh từ internet hoặc để trống để dùng ảnh mặc định</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="featured">
                                <label class="form-check-label" for="featured">
                                    Sản phẩm nổi bật
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    const form = document.querySelector('#productModal form');
    form.reset();
    
    // Remove ID field
    const idField = form.querySelector('input[name="id"]');
    if (idField) {
        idField.remove();
    }
    
    // Reset modal title
    document.querySelector('#productModal .modal-title').textContent = 'Thêm sản phẩm mới';
    
    // Remove preview image
    const preview = form.querySelector('.img-thumbnail');
    if (preview) {
        preview.parentElement.remove();
    }
}

function editProduct(product) {
    const form = document.querySelector('#productModal form');
    
    // Reset form first
    resetForm();
    
    // Set modal title
    document.querySelector('#productModal .modal-title').textContent = 'Sửa sản phẩm';
    
    // Add or update ID field
    let idField = form.querySelector('input[name="id"]');
    if (!idField) {
        idField = document.createElement('input');
        idField.type = 'hidden';
        idField.name = 'id';
        form.insertBefore(idField, form.firstChild);
    }
    idField.value = product.id;
    
    // Fill form fields
    form.querySelector('input[name="name"]').value = product.name || '';
    form.querySelector('input[name="stock"]').value = product.stock || 0;
    form.querySelector('select[name="category_id"]').value = product.category_id || '';
    form.querySelector('select[name="brand_id"]').value = product.brand_id || '';
    form.querySelector('input[name="price"]').value = product.price || '';
    form.querySelector('input[name="discount_price"]').value = product.discount_price || '';
    form.querySelector('input[name="cpu"]').value = product.cpu || '';
    form.querySelector('input[name="ram"]').value = product.ram || '';
    form.querySelector('input[name="storage"]').value = product.storage || '';
    form.querySelector('input[name="screen"]').value = product.screen || '';
    form.querySelector('input[name="graphics"]').value = product.graphics || '';
    form.querySelector('input[name="os"]').value = product.os || '';
    form.querySelector('input[name="weight"]').value = product.weight || '';
    form.querySelector('textarea[name="description"]').value = product.description || '';
    form.querySelector('input[name="image"]').value = product.image || '';
    form.querySelector('input[name="is_featured"]').checked = product.is_featured == 1;
    form.querySelector('select[name="status"]').value = product.status || 'active';
    
    // Show image preview if exists
    if (product.image) {
        const imageField = form.querySelector('input[name="image"]');
        const previewDiv = document.createElement('div');
        previewDiv.className = 'mt-2';
        previewDiv.innerHTML = `<img src="${product.image}" class="img-thumbnail" width="150" 
                                     onerror="this.src='https://placehold.co/150x150/e9ecef/6c757d?text=No+Image'">`;
        imageField.parentElement.appendChild(previewDiv);
    }
    
    // Show modal
    new bootstrap.Modal(document.getElementById('productModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>

