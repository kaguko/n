<?php
/**
 * Register Page - Trang đăng ký
 */

// Redirect nếu đã đăng nhập
if (is_logged_in()) {
    redirect(base_url('index.php'));
}

// Xử lý đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = "Vui lòng nhập tên đăng nhập";
    } elseif (strlen($username) < 3) {
        $errors[] = "Tên đăng nhập phải có ít nhất 3 ký tự";
    }
    
    if (empty($email)) {
        $errors[] = "Vui lòng nhập email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($password)) {
        $errors[] = "Vui lòng nhập mật khẩu";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    // Kiểm tra username đã tồn tại
    if (empty($errors)) {
        $existing = db_fetch_one("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            $errors[] = "Tên đăng nhập đã được sử dụng";
        }
        
        $existing = db_fetch_one("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $errors[] = "Email đã được đăng ký";
        }
    }
    
    if (empty($errors)) {
        // Tạo tài khoản mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $user_id = db_insert("
            INSERT INTO users (username, email, password, full_name, phone, role)
            VALUES (?, ?, ?, ?, ?, 'user')
        ", [$username, $email, $hashed_password, $full_name, $phone]);
        
        if ($user_id) {
            // Đăng nhập luôn
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'user';
            
            set_flash('success', 'Đăng ký thành công! Chào mừng ' . $username);
            redirect(base_url('index.php'));
        } else {
            set_flash('danger', 'Đăng ký thất bại. Vui lòng thử lại!');
        }
    } else {
        set_flash('danger', implode('<br>', $errors));
    }
}

$page_title = 'Đăng ký';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus display-3 text-primary"></i>
                        <h2 class="mt-3">Đăng ký tài khoản</h2>
                        <p class="text-muted">Tạo tài khoản để mua sắm dễ dàng hơn</p>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username" 
                                       value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>" 
                                       required autofocus>
                            </div>
                            <small class="text-muted">Ít nhất 3 ký tự</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" 
                                       value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" name="full_name" 
                                       value="<?php echo isset($_POST['full_name']) ? e($_POST['full_name']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <small class="text-muted">Ít nhất 6 ký tự</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree" required>
                            <label class="form-check-label" for="agree">
                                Tôi đồng ý với <a href="#">Điều khoản sử dụng</a>
                            </label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-person-plus"></i> Đăng ký
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản? 
                                <a href="<?php echo base_url('index.php?page=login'); ?>">Đăng nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
