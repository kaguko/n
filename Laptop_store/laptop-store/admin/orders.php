<?php
/**
 * Admin Orders - Quản lý đơn hàng
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Quản lý đơn hàng';

// Xử lý cập nhật trạng thái
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    db_query("UPDATE orders SET status = ? WHERE id = ?", [$status, $order_id]);
    set_flash('success', 'Đã cập nhật trạng thái đơn hàng!');
    redirect('orders.php');
}

// Xem chi tiết đơn hàng
$view_order = null;
$order_items = [];
if (isset($_GET['view'])) {
    $order_id = intval($_GET['view']);
    $view_order = db_fetch_one("
        SELECT o.*, u.username
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ", [$order_id]);
    
    if ($view_order) {
        $order_items = db_fetch_all("
            SELECT * FROM order_items WHERE order_id = ?
        ", [$order_id]);
    }
}

// Lấy danh sách đơn hàng
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$where = [];
$params = [];

if ($filter_status) {
    $where[] = "status = ?";
    $params[] = $filter_status;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$orders = db_fetch_all("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    $where_clause
    ORDER BY o.created_at DESC
", $params);

include 'includes/header.php';
?>

<!-- Filter -->
<div class="mb-4">
    <div class="btn-group" role="group">
        <a href="orders.php" class="btn btn-outline-primary <?php echo !$filter_status ? 'active' : ''; ?>">
            Tất cả (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders")['c']; ?>)
        </a>
        <a href="orders.php?status=pending" class="btn btn-outline-warning <?php echo $filter_status == 'pending' ? 'active' : ''; ?>">
            Chờ xử lý (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders WHERE status='pending'")['c']; ?>)
        </a>
        <a href="orders.php?status=processing" class="btn btn-outline-info <?php echo $filter_status == 'processing' ? 'active' : ''; ?>">
            Đang xử lý (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders WHERE status='processing'")['c']; ?>)
        </a>
        <a href="orders.php?status=shipped" class="btn btn-outline-primary <?php echo $filter_status == 'shipped' ? 'active' : ''; ?>">
            Đã gửi (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders WHERE status='shipped'")['c']; ?>)
        </a>
        <a href="orders.php?status=delivered" class="btn btn-outline-success <?php echo $filter_status == 'delivered' ? 'active' : ''; ?>">
            Hoàn thành (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders WHERE status='delivered'")['c']; ?>)
        </a>
        <a href="orders.php?status=cancelled" class="btn btn-outline-danger <?php echo $filter_status == 'cancelled' ? 'active' : ''; ?>">
            Đã hủy (<?php echo db_fetch_one("SELECT COUNT(*) as c FROM orders WHERE status='cancelled'")['c']; ?>)
        </a>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Email/SĐT</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                            <h5 class="text-muted">Chưa có đơn hàng nào</h5>
                            <p class="text-muted">Khách hàng sẽ đặt hàng từ trang web.</p>
                            <a href="../index.php" target="_blank" class="btn btn-primary">
                                <i class="bi bi-house"></i> Xem trang web
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <a href="?view=<?php echo $order['id']; ?>">
                                <strong><?php echo e($order['order_number']); ?></strong>
                            </a>
                        </td>
                        <td><?php echo e($order['customer_name']); ?></td>
                        <td>
                            <?php echo e($order['customer_email']); ?><br>
                            <small><?php echo e($order['customer_phone']); ?></small>
                        </td>
                        <td class="fw-bold text-danger"><?php echo format_price($order['total_amount']); ?></td>
                        <td>
                            <?php
                            $badges = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ];
                            $badge = $badges[$order['status']] ?? 'secondary';
                            
                            $status_names = [
                                'pending' => 'Chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'shipped' => 'Đã gửi',
                                'delivered' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy'
                            ];
                            ?>
                            <span class="badge bg-<?php echo $badge; ?>">
                                <?php echo $status_names[$order['status']]; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $order['payment_method'] == 'cod' ? 'COD' : 'Chuyển khoản'; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                        <td>
                            <a href="?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<?php if ($view_order): ?>
<div class="modal fade show" id="orderModal" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng #<?php echo e($view_order['order_number']); ?></h5>
                <a href="orders.php" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Thông tin khách hàng</h6>
                        <p class="mb-1"><strong>Họ tên:</strong> <?php echo e($view_order['customer_name']); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo e($view_order['customer_email']); ?></p>
                        <p class="mb-1"><strong>SĐT:</strong> <?php echo e($view_order['customer_phone']); ?></p>
                        <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo e($view_order['shipping_address']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin đơn hàng</h6>
                        <p class="mb-1"><strong>Mã đơn:</strong> <?php echo e($view_order['order_number']); ?></p>
                        <p class="mb-1"><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($view_order['created_at'])); ?></p>
                        <p class="mb-1"><strong>Thanh toán:</strong> <?php echo $view_order['payment_method'] == 'cod' ? 'COD' : 'Chuyển khoản'; ?></p>
                        <p class="mb-1"><strong>Ghi chú:</strong> <?php echo e($view_order['notes']); ?></p>
                    </div>
                </div>

                <hr>

                <h6>Sản phẩm</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo e($item['product_name']); ?></td>
                            <td><?php echo format_price($item['product_price']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo format_price($item['subtotal']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Tổng cộng:</th>
                            <th><?php echo format_price($view_order['total_amount']); ?></th>
                        </tr>
                    </tfoot>
                </table>

                <hr>

                <form method="POST" class="mt-3">
                    <input type="hidden" name="order_id" value="<?php echo $view_order['id']; ?>">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Cập nhật trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="pending" <?php echo $view_order['status'] == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                <option value="processing" <?php echo $view_order['status'] == 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="shipped" <?php echo $view_order['status'] == 'shipped' ? 'selected' : ''; ?>>Đã gửi hàng</option>
                                <option value="delivered" <?php echo $view_order['status'] == 'delivered' ? 'selected' : ''; ?>>Đã giao hàng</option>
                                <option value="cancelled" <?php echo $view_order['status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="update_status" class="btn btn-primary w-100">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
