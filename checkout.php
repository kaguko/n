<?php
/**
 * Checkout Page - Trang thanh toán
 */

// Xử lý đặt hàng TRƯỚC (để có thể redirect)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $cart = get_cart();
    
    // Check giỏ hàng trống
    if (empty($cart)) {
        set_flash('danger', 'Giỏ hàng trống!');
        redirect(base_url('index.php?page=cart'));
    }
    
    // Lấy thông tin sản phẩm
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $products = db_fetch_all("
        SELECT id, name, price, discount_price, stock
        FROM products
        WHERE id IN ($placeholders)
    ", $ids);
    
    $cart_items = [];
    $total = 0;
    
    foreach ($products as $product) {
        $price = $product['discount_price'] ?? $product['price'];
        $quantity = $cart[$product['id']];
        
        // Check stock
        if ($product['stock'] < $quantity) {
            set_flash('danger', "Sản phẩm '{$product['name']}' chỉ còn {$product['stock']} trong kho!");
            redirect(base_url('index.php?page=cart'));
        }
        
        $subtotal = $price * $quantity;
        $cart_items[] = [
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes'] ?? '');
    
    $errors = [];
    
    if (empty($customer_name)) $errors[] = "Vui lòng nhập họ tên";
    if (empty($customer_email)) $errors[] = "Vui lòng nhập email";
    if (empty($customer_phone)) $errors[] = "Vui lòng nhập số điện thoại";
    if (empty($shipping_address)) $errors[] = "Vui lòng nhập địa chỉ giao hàng";
    
    if (empty($errors)) {
        // Tạo đơn hàng
        $order_number = generate_order_number();
        $user_id = is_logged_in() ? $_SESSION['user_id'] : null;
        
        $order_id = db_insert("
            INSERT INTO orders (user_id, order_number, customer_name, customer_email, customer_phone, shipping_address, total_amount, payment_method, notes, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ", [$user_id, $order_number, $customer_name, $customer_email, $customer_phone, $shipping_address, $total, $payment_method, $notes]);
        
        if ($order_id) {
            // Thêm order items
            foreach ($cart_items as $item) {
                db_query("
                    INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, subtotal)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [$order_id, $item['product_id'], $item['product_name'], $item['price'], $item['quantity'], $item['subtotal']]);
                
                // Giảm stock
                db_query("UPDATE products SET stock = stock - ? WHERE id = ?", [$item['quantity'], $item['product_id']]);
            }
            
            // Xóa giỏ hàng
            clear_cart();
            
            set_flash('success', "Đặt hàng thành công! Mã đơn hàng: $order_number");
            redirect(base_url('index.php?page=order-success&order=' . $order_number));
        } else {
            set_flash('danger', 'Đặt hàng thất bại. Vui lòng thử lại!');
            redirect(base_url('index.php?page=checkout'));
        }
    } else {
        set_flash('danger', implode('<br>', $errors));
        redirect(base_url('index.php?page=checkout'));
    }
}

// Kiểm tra giỏ hàng (cho GET request)
$cart = get_cart();
if (empty($cart)) {
    redirect(base_url('index.php?page=cart'));
}

// Lấy thông tin sản phẩm trong giỏ hàng để hiển thị
$cart_items = [];
$total = 0;

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$products = db_fetch_all("
    SELECT id, name, price, discount_price, image, stock
    FROM products
    WHERE id IN ($placeholders)
", $ids);

foreach ($products as $product) {
    $price = $product['discount_price'] ?? $product['price'];
    $quantity = $cart[$product['id']];
    $subtotal = $price * $quantity;
    
    // Check stock
    if ($product['stock'] < $quantity) {
        set_flash('danger', "Sản phẩm '{$product['name']}' chỉ còn {$product['stock']} trong kho!");
        redirect(base_url('index.php?page=cart'));
    }
    
    $cart_items[] = [
        'product_id' => $product['id'],
        'product_name' => $product['name'],
        'price' => $price,
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
    
    $total += $subtotal;
}

$page_title = 'Thanh toán';

// Lấy thông tin user nếu đã đăng nhập
$user = current_user();
if ($user) {
    $user_info = db_fetch_one("SELECT * FROM users WHERE id = ?", [$user['id']]);
}
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php?page=cart'); ?>">Giỏ hàng</a></li>
            <li class="breadcrumb-item active">Thanh toán</li>
        </ol>
    </nav>

    <h2 class="mb-4"><i class="bi bi-credit-card"></i> Thanh toán đơn hàng</h2>

    <form method="POST">
        <div class="row">
            <!-- Thông tin khách hàng -->
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="customer_name" 
                                   value="<?php echo isset($user_info) ? e($user_info['full_name'] ?? '') : ''; ?>" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="customer_email" 
                                       value="<?php echo isset($user_info) ? e($user_info['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="customer_phone" 
                                       value="<?php echo isset($user_info) ? e($user_info['phone'] ?? '') : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="shipping_address" rows="3" required><?php echo isset($user_info) ? e($user_info['address'] ?? '') : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian giao hàng"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash"></i> Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" id="bank">
                                <label class="form-check-label" for="bank">
                                    <i class="bi bi-bank"></i> Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng của bạn -->
            <div class="col-md-5">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Đơn hàng của bạn</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-end">Tạm tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <?php echo e($item['product_name']); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo format_price($item['price']); ?> × <?php echo $item['quantity']; ?>
                                            </small>
                                        </td>
                                        <td class="text-end">
                                            <?php echo format_price($item['subtotal']); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th>Tạm tính:</th>
                                        <td class="text-end" id="checkout-subtotal"><?php echo format_price($total); ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <th>Phí vận chuyển:</th>
                                        <td class="text-end text-success">Miễn phí</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Tổng cộng:</th>
                                        <th class="text-end text-danger">
                                            <h4 class="mb-0" id="checkout-total"><?php echo format_price($total); ?></h4>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="place_order" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Đặt hàng
                            </button>
                            <a href="<?php echo base_url('index.php?page=cart'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
