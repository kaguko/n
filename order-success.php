<?php
/**
 * Order Success Page - Trang đặt hàng thành công
 */

if (!isset($_GET['order'])) {
    redirect(base_url('index.php'));
}

$order_number = $_GET['order'];
$order = db_fetch_one("SELECT * FROM orders WHERE order_number = ?", [$order_number]);

if (!$order) {
    redirect(base_url('index.php'));
}

$page_title = 'Đặt hàng thành công';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Đặt hàng thành công!</h2>
                    
                    <p class="lead">Cảm ơn bạn đã đặt hàng tại Laptop Store</p>
                    
                    <div class="alert alert-info">
                        <strong>Mã đơn hàng:</strong> <?php echo e($order['order_number']); ?><br>
                        <strong>Tổng tiền:</strong> <?php echo format_price($order['total_amount']); ?><br>
                        <strong>Trạng thái:</strong> <span class="badge bg-warning">Đang xử lý</span>
                    </div>
                    
                    <p>
                        Chúng tôi đã nhận được đơn hàng của bạn và sẽ liên hệ sớm nhất để xác nhận.
                        <br>Thông tin chi tiết đơn hàng đã được gửi đến email: <strong><?php echo e($order['customer_email']); ?></strong>
                    </p>
                    
                    <div class="mt-4">
                        <a href="<?php echo base_url('index.php'); ?>" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-house"></i> Về trang chủ
                        </a>
                        <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-shop"></i> Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
