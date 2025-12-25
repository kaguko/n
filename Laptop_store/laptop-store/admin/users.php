<?php
/**
 * Admin Users - Quản lý người dùng
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Quản lý người dùng';

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    db_query("DELETE FROM users WHERE id = ? AND id != ?", [$id, $_SESSION['user_id']]);
    set_flash('success', 'Đã xóa người dùng!');
    redirect('users.php');
}

// Xử lý thay đổi role
if (isset($_POST['change_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    db_query("UPDATE users SET role = ? WHERE id = ?", [$new_role, $user_id]);
    set_flash('success', 'Đã cập nhật quyền người dùng!');
    redirect('users.php');
}

// Lấy danh sách người dùng
$users = db_fetch_all("
    SELECT u.*, 
           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
           (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id) as total_spent
    FROM users u
    ORDER BY u.created_at DESC
");

include 'includes/header.php';
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Họ tên</th>
                        <th>SĐT</th>
                        <th>Quyền</th>
                        <th>Đơn hàng</th>
                        <th>Tổng chi</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo e($user['username']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['full_name']); ?></td>
                        <td><?php echo e($user['phone']); ?></td>
                        <td>
                            <?php if ($user['role'] == 'admin'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user['order_count']; ?></td>
                        <td><?php echo format_price($user['total_spent'] ?? 0); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#roleModal<?php echo $user['id']; ?>">
                                    <i class="bi bi-key"></i>
                                </button>
                                <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="badge bg-info">Bạn</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Role Modal -->
                    <div class="modal fade" id="roleModal<?php echo $user['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Thay đổi quyền: <?php echo e($user['username']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <label class="form-label">Quyền</label>
                                        <select class="form-select" name="role">
                                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" name="change_role" class="btn btn-primary">Lưu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
