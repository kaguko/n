<?php
/**
 * Add to Cart Handler - Xử lý thêm vào giỏ hàng
 */

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    add_to_cart($product_id, 1);
    set_flash('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    redirect(base_url('index.php?page=cart'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    add_to_cart($product_id, $quantity);
    
    if (isset($_POST['buy_now'])) {
        redirect(base_url('index.php?page=checkout'));
    } else {
        set_flash('success', 'Đã thêm sản phẩm vào giỏ hàng!');
        redirect(base_url('index.php?page=cart'));
    }
}

redirect(base_url('index.php'));
