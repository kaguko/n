<?php
/**
 * Admin Categories - Quản lý danh mục
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Quản lý danh mục';

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM categories WHERE id = ?", [$id]);
    set_flash('success', 'Đã xóa danh mục!');
    redirect('categories.php');
}

// Xử lý thêm/sửa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = create_slug($name);
    
    if ($id > 0) {
        db_query("UPDATE categories SET name=?, slug=?, description=? WHERE id=?", [$name, $slug, $description, $id]);
        set_flash('success', 'Đã cập nhật danh mục!');
    } else {
        db_insert("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)", [$name, $slug, $description]);
        set_flash('success', 'Đã thêm danh mục mới!');
    }
    
    redirect('categories.php');
}

$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_category = db_fetch_one("SELECT * FROM categories WHERE id = ?", [$edit_id]);
}

$categories = db_fetch_all("
    SELECT c.*, COUNT(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
");

include 'includes/header.php';
?>

<div class="mb-4">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetForm()">
        <i class="bi bi-plus-circle"></i> Thêm danh mục mới
    </button>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Mô tả</th>
                        <th>Số sản phẩm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><strong><?php echo e($cat['name']); ?></strong></td>
                        <td><code><?php echo e($cat['slug']); ?></code></td>
                        <td><?php echo e($cat['description']); ?></td>
                        <td><span class="badge bg-info"><?php echo $cat['product_count']; ?></span></td>
                        <td>
                            <a href="?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="?delete=<?php echo $cat['id']; ?>" class="btn btn-sm btn-danger" 
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $edit_category ? 'Sửa danh mục' : 'Thêm danh mục mới'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục *</label>
                        <input type="text" class="form-control" name="name" 
                               value="<?php echo $edit_category ? e($edit_category['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo $edit_category ? e($edit_category['description']) : ''; ?></textarea>
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

<?php if ($edit_category): ?>
window.onload = function() {
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
