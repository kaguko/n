<?php
/**
 * Cart Page - Trang giỏ hàng
 */

// Xử lý update cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            update_cart($product_id, intval($quantity));
        }
        set_flash('success', 'Cập nhật giỏ hàng thành công!');
        redirect(base_url('index.php?page=cart'));
    }
    
    if (isset($_POST['proceed_checkout'])) {
        // Cập nhật số lượng từ form trước khi chuyển sang checkout
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $product_id => $quantity) {
                update_cart($product_id, intval($quantity));
            }
        }
        redirect(base_url('index.php?page=checkout'));
    }
    
    if (isset($_POST['remove_item'])) {
        $product_id = intval($_POST['product_id']);
        remove_from_cart($product_id);
        set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
        redirect(base_url('index.php?page=cart'));
    }
}

$page_title = 'Giỏ hàng';
$cart = get_cart();

// Lấy thông tin sản phẩm trong giỏ hàng
$cart_items = [];
$total = 0;

if (!empty($cart)) {
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
        
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal
        ];
        
        $total += $subtotal;
    }
}
?>

<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giỏ hàng</li>
        </ol>
    </nav>

    <h2 class="mb-4"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Giỏ hàng của bạn đang trống.
        </div>
        <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
        </a>
    <?php else: ?>
        <form method="POST">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th width="15%">Đơn giá</th>
                            <th width="12%">Số lượng</th>
                            <th width="15%">Thành tiền</th>
                            <th width="8%">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?php echo get_image_url($item['product']['image']); ?>" 
                                     class="img-thumbnail" 
                                     alt="<?php echo e($item['product']['name']); ?>"
                                     style="max-width: 80px;"
>
                            </td>
                            <td>
                                <a href="<?php echo base_url('index.php?page=product-detail&id=' . $item['product']['id']); ?>" 
                                   class="text-decoration-none">
                                    <?php echo e($item['product']['name']); ?>
                                </a>
                                <?php if ($item['product']['stock'] <= 0): ?>
                                    <br><span class="badge bg-danger">Hết hàng</span>
                                <?php elseif ($item['product']['stock'] < $item['quantity']): ?>
                                    <br><span class="badge bg-warning">Chỉ còn <?php echo $item['product']['stock']; ?> sản phẩm</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php echo format_price($item['price']); ?>
                            </td>
                            <td>
                                <input type="number" 
                                       class="form-control quantity-input" 
                                       name="quantity[<?php echo $item['product']['id']; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['product']['stock']; ?>"
                                       data-price="<?php echo $item['price']; ?>"
                                       data-product-id="<?php echo $item['product']['id']; ?>"
                                       onchange="updateSubtotal(this)">
                            </td>
                            <td class="text-end fw-bold text-danger subtotal-<?php echo $item['product']['id']; ?>">
                                <?php echo format_price($item['subtotal']); ?>
                            </td>
                            <td class="text-center">
                                <button type="submit" name="remove_item" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td class="text-end">
                                <h4 class="text-danger mb-0" id="grand-total"><?php echo format_price($total); ?></h4>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="<?php echo base_url('index.php?page=products'); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" name="update_cart" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Cập nhật giỏ hàng
                    </button>
                    <button type="submit" name="proceed_checkout" class="btn btn-success">
                        <i class="bi bi-credit-card"></i> Thanh toán
                    </button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function formatPrice(price) {
    // Format number với dấu phân cách hàng nghìn
    const formatted = Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return formatted + ' ₫';
}

function updateSubtotal(input) {
    const quantity = parseInt(input.value) || 1;
    const price = parseFloat(input.getAttribute('data-price'));
    const productId = input.getAttribute('data-product-id');
    
    // Validate quantity
    if (quantity < 1) {
        input.value = 1;
        return;
    }
    
    const max = parseInt(input.getAttribute('max'));
    if (max && quantity > max) {
        input.value = max;
        alert('Số lượng không được vượt quá ' + max);
        return;
    }
    
    // Tính subtotal
    const subtotal = quantity * price;
    
    // Update subtotal cell
    const subtotalCell = document.querySelector('.subtotal-' + productId);
    if (subtotalCell) {
        subtotalCell.textContent = formatPrice(subtotal);
    }
    
    // Update grand total
    updateGrandTotal();
}

function updateGrandTotal() {
    let grandTotal = 0;
    
    // Tính tổng tất cả các subtotal
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        const quantity = parseInt(input.value) || 1;
        const price = parseFloat(input.getAttribute('data-price'));
        grandTotal += quantity * price;
    });
    
    // Update grand total display
    const grandTotalElement = document.getElementById('grand-total');
    if (grandTotalElement) {
        grandTotalElement.textContent = formatPrice(grandTotal);
    }
}

// Thêm event listener cho các input (backup nếu onchange không work)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('input', function() {
            updateSubtotal(this);
        });
    });
});
</script>
