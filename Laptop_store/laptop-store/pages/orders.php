<?php
/**
 * Orders Page - Trang đơn hàng của user
 */

// Yêu cầu đăng nhập
require_login();

$page_title = 'Đơn hàng của tôi';
$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user
$orders = db_fetch_all("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
", [$user_id]);
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Đơn hàng của tôi</li>
        </ol>
    </nav>

    <h2 class="mb-4"><i class="bi bi-bag-check"></i> Đơn hàng của tôi</h2>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Bạn chưa có đơn hàng nào.
        </div>
        <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
        </a>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo e($order['order_number']); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo format_price($order['total_amount']); ?></td>
                            <td>
                                <?php
                                $status_badges = [
                                    'pending' => ['badge' => 'warning', 'text' => 'Chờ xử lý'],
                                    'processing' => ['badge' => 'info', 'text' => 'Đang xử lý'],
                                    'shipped' => ['badge' => 'primary', 'text' => 'Đang giao'],
                                    'delivered' => ['badge' => 'success', 'text' => 'Đã giao'],
                                    'cancelled' => ['badge' => 'danger', 'text' => 'Đã hủy']
                                ];
                                $badge = $status_badges[$order['status']] ?? ['badge' => 'secondary', 'text' => $order['status']];
                                ?>
                                <span class="badge bg-<?php echo $badge['badge']; ?>">
                                    <?php echo $badge['text']; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $order['id']; ?>">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Chi tiết đơn hàng -->
                        <div class="modal fade" id="orderModal<?php echo $order['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chi tiết đơn hàng #<?php echo e($order['order_number']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Lấy chi tiết đơn hàng
                                        $order_items = db_fetch_all("
                                            SELECT * FROM order_items WHERE order_id = ?
                                        ", [$order['id']]);
                                        ?>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6>Thông tin khách hàng</h6>
                                                <p class="mb-1"><strong>Họ tên:</strong> <?php echo e($order['customer_name']); ?></p>
                                                <p class="mb-1"><strong>Email:</strong> <?php echo e($order['customer_email']); ?></p>
                                                <p class="mb-1"><strong>SĐT:</strong> <?php echo e($order['customer_phone']); ?></p>
                                                <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo e($order['shipping_address']); ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Thông tin đơn hàng</h6>
                                                <p class="mb-1"><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                                <p class="mb-1"><strong>Trạng thái:</strong> 
                                                    <span class="badge bg-<?php echo $badge['badge']; ?>"><?php echo $badge['text']; ?></span>
                                                </p>
                                                <p class="mb-1"><strong>Phương thức:</strong> <?php echo e($order['payment_method']); ?></p>
                                                <?php if (!empty($order['notes'])): ?>
                                                    <p class="mb-1"><strong>Ghi chú:</strong> <?php echo e($order['notes']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <h6>Sản phẩm</h6>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tên sản phẩm</th>
                                                    <th class="text-end">Đơn giá</th>
                                                    <th class="text-center">SL</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($order_items as $item): ?>
                                                    <tr>
                                                        <td><?php echo e($item['product_name']); ?></td>
                                                        <td class="text-end"><?php echo format_price($item['product_price']); ?></td>
                                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                                        <td class="text-end"><?php echo format_price($item['subtotal']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-end">Tổng cộng:</th>
                                                    <th class="text-end"><?php echo format_price($order['total_amount']); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
