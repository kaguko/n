<?php
/**
 * Admin Index - Trang chủ admin dashboard
 */
require_once '../config/database.php';
require_once '../config/functions.php';

require_admin();

$page_title = 'Admin Dashboard';

// Thống kê
$total_products = db_fetch_one("SELECT COUNT(*) as count FROM products")['count'];
$total_users = db_fetch_one("SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'];
$total_orders = db_fetch_one("SELECT COUNT(*) as count FROM orders")['count'];
$total_revenue = db_fetch_one("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'")['total'] ?? 0;

// Đơn hàng gần đây
$recent_orders = db_fetch_all("
    SELECT o.*, u.username
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Sản phẩm bán chạy
$top_products = db_fetch_all("
    SELECT p.name, SUM(oi.quantity) as total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");

include 'includes/header.php';
?>

<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Sản phẩm</h6>
                        <h2 class="mb-0"><?php echo number_format($total_products); ?></h2>
                    </div>
                    <i class="bi bi-laptop display-4"></i>
                </div>
            </div>
            <div class="card-footer bg-primary border-0">
                <a href="products.php" class="text-white text-decoration-none small">
                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Người dùng</h6>
                        <h2 class="mb-0"><?php echo number_format($total_users); ?></h2>
                    </div>
                    <i class="bi bi-people display-4"></i>
                </div>
            </div>
            <div class="card-footer bg-success border-0">
                <a href="users.php" class="text-white text-decoration-none small">
                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Đơn hàng</h6>
                        <h2 class="mb-0"><?php echo number_format($total_orders); ?></h2>
                    </div>
                    <i class="bi bi-bag-check display-4"></i>
                </div>
            </div>
            <div class="card-footer bg-warning border-0">
                <a href="orders.php" class="text-white text-decoration-none small">
                    Xem chi tiết <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Doanh thu</h6>
                        <h2 class="mb-0"><?php echo number_format($total_revenue / 1000000, 1); ?>M</h2>
                    </div>
                    <i class="bi bi-currency-dollar display-4"></i>
                </div>
            </div>
            <div class="card-footer bg-danger border-0">
                <small class="text-white">Tổng doanh thu: <?php echo format_price($total_revenue); ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Đơn hàng gần đây</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><a href="orders.php?view=<?php echo $order['id']; ?>"><?php echo e($order['order_number']); ?></a></td>
                                <td><?php echo e($order['customer_name']); ?></td>
                                <td><?php echo format_price($order['total_amount']); ?></td>
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
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($order['status']); ?></span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-trophy"></i> Sản phẩm bán chạy</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($top_products as $index => $product): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary rounded-pill me-2">#<?php echo $index + 1; ?></span>
                            <?php echo e($product['name']); ?>
                        </div>
                        <span class="badge bg-success"><?php echo $product['total_sold']; ?> đã bán</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
