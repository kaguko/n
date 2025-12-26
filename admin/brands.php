<?php
/**
 * Admin Brands - Quản lý hãng
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Quản lý hãng';

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM brands WHERE id = ?", [$id]);
    set_flash('success', 'Đã xóa hãng!');
    redirect('brands.php');
}

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $slug = create_slug($name);
    
    if ($id > 0) {
        db_query("UPDATE brands SET name=?, slug=? WHERE id=?", [$name, $slug, $id]);
        set_flash('success', 'Đã cập nhật hãng!');
    } else {
        db_insert("INSERT INTO brands (name, slug) VALUES (?, ?)", [$name, $slug]);
        set_flash('success', 'Đã thêm hãng mới!');
    }
    
    redirect('brands.php');
}

$edit_brand = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_brand = db_fetch_one("SELECT * FROM brands WHERE id = ?", [$edit_id]);
}

$brands = db_fetch_all("
    SELECT b.*, COUNT(p.id) as product_count
    FROM brands b
    LEFT JOIN products p ON b.id = p.brand_id
    GROUP BY b.id
    ORDER BY b.name
");

include 'includes/header.php';
?>

<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="resetForm()">
        <i class="bi bi-plus-circle"></i> Thêm hãng mới
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên hãng</th>
                        <th>Slug</th>
                        <th>Số sản phẩm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?php echo $brand['id']; ?></td>
                        <td><strong><?php echo e($brand['name']); ?></strong></td>
                        <td><code><?php echo e($brand['slug']); ?></code></td>
                        <td><span class="badge bg-info"><?php echo $brand['product_count']; ?></span></td>
                        <td>
                            <a href="?edit=<?php echo $brand['id']; ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#brandModal">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?delete=<?php echo $brand['id']; ?>" class="btn btn-sm btn-danger" 
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

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $edit_brand ? 'Sửa hãng' : 'Thêm hãng mới'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($edit_brand): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_brand['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên hãng *</label>
                        <input type="text" class="form-control" name="name" 
                               value="<?php echo $edit_brand ? e($edit_brand['name']) : ''; ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.querySelector('form').reset();
    document.querySelector('input[name="id"]')?.remove();
}

<?php if ($edit_brand): ?>
window.onload = function() {
    new bootstrap.Modal(document.getElementById('brandModal')).show();
}
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
