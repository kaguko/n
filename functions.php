<?php
/**
 * Functions - Các hàm tiện ích chung
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect đến một URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Lấy URL gốc của website
 */
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $base = dirname($_SERVER['SCRIPT_NAME']);
    
    // Xóa /index.php nếu có
    $base = str_replace('/index.php', '', $base);
    
    return $protocol . "://" . $host . $base . "/" . ltrim($path, '/');
}

/**
 * Escape HTML để chống XSS
 */
function e($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra user có phải admin không
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login, nếu chưa đăng nhập thì redirect
 */
function require_login() {
    if (!is_logged_in()) {
        redirect(base_url('index.php?page=login'));
    }
}

/**
 * Require admin, nếu không phải admin thì redirect
 */
function require_admin() {
    if (!is_admin()) {
        redirect(base_url('index.php'));
    }
}

/**
 * Lấy thông tin user hiện tại
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get và xóa flash message
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format giá tiền VNĐ
 */
function format_price($price) {
    return number_format($price, 0, ',', '.') . ' ₫';
}

/**
 * Tạo slug từ string
 */
function create_slug($string) {
    $string = strtolower(trim($string));
    
    // Chuyển ký tự có dấu thành không dấu
    $vietnamese = [
        'á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
        'đ',
        'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
        'í', 'ì', 'ỉ', 'ĩ', 'ị',
        'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
        'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
        'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ'
    ];
    
    $english = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'd',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y'
    ];
    
    $string = str_replace($vietnamese, $english, $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    
    return trim($string, '-');
}

/**
 * Upload file ảnh
 */
function upload_image($file, $folder = 'products') {
    $target_dir = __DIR__ . "/../assets/uploads/" . $folder . "/";
    
    // Tạo thư mục nếu chưa có
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Kiểm tra file có phải là ảnh không
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_extension, $allowed_types)) {
        return ['success' => false, 'message' => 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF, WEBP'];
    }
    
    // Kiểm tra kích thước file (max 5MB)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File ảnh quá lớn (tối đa 5MB)'];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return [
            'success' => true,
            'filename' => $folder . '/' . $new_filename,
            'path' => $target_file
        ];
    }
    
    return ['success' => false, 'message' => 'Upload file thất bại'];
}

/**
 * Lấy URL ảnh
 */
function get_image_url($filename) {
    if (empty($filename)) {
        return 'https://placehold.co/500x400/e9ecef/6c757d?text=No+Image';
    }
    
    // Nếu là URL (bắt đầu với http/https), return trực tiếp
    if (preg_match('/^https?:\/\//i', $filename)) {
        return $filename;
    }
    
    // Kiểm tra file local có tồn tại không
    $filepath = __DIR__ . '/../assets/uploads/' . $filename;
    if (file_exists($filepath)) {
        return base_url('assets/uploads/' . $filename);
    }
    
    // Nếu không có, dùng placeholder
    return 'https://placehold.co/500x400/e9ecef/6c757d?text=Laptop';
}

/**
 * Phân trang
 */
function paginate($total, $per_page, $current_page) {
    $total_pages = ceil($total / $per_page);
    
    return [
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages
    ];
}

/**
 * Get cart từ session
 */
function get_cart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

/**
 * Thêm sản phẩm vào giỏ hàng
 */
function add_to_cart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

/**
 * Cập nhật số lượng trong giỏ hàng
 */
function update_cart($product_id, $quantity) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

/**
 * Xóa sản phẩm khỏi giỏ hàng
 */
function remove_from_cart($product_id) {
    unset($_SESSION['cart'][$product_id]);
}

/**
 * Xóa toàn bộ giỏ hàng
 */
function clear_cart() {
    unset($_SESSION['cart']);
}

/**
 * Đếm số lượng sản phẩm trong giỏ hàng
 */
function cart_count() {
    $cart = get_cart();
    return array_sum($cart);
}

/**
 * Tính tổng tiền giỏ hàng
 */
function cart_total() {
    global $conn;
    $cart = get_cart();
    $total = 0;
    
    if (empty($cart)) {
        return 0;
    }
    
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    $sql = "SELECT id, price, discount_price FROM products WHERE id IN ($placeholders)";
    $products = db_fetch_all($sql, $ids);
    
    foreach ($products as $product) {
        $price = $product['discount_price'] ?? $product['price'];
        $quantity = $cart[$product['id']];
        $total += $price * $quantity;
    }
    
    return $total;
}

/**
 * Tạo mã đơn hàng
 */
function generate_order_number() {
    return 'ORD' . date('YmdHis') . rand(1000, 9999);
}
