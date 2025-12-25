<?php
/**
 * Login Page - Trang đăng nhập
 */

// Redirect nếu đã đăng nhập
if (is_logged_in()) {
    redirect(base_url('index.php'));
}

// Xử lý login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        set_flash('danger', 'Vui lòng nhập đầy đủ thông tin!');
    } else {
        $user = db_fetch_one("SELECT * FROM users WHERE username = ? OR email = ?", [$username, $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['username']);
            
            // Redirect về trang trước đó hoặc trang chủ
            $redirect_to = $_GET['redirect'] ?? 'index.php';
            redirect(base_url($redirect_to));
        } else {
            set_flash('danger', 'Tên đăng nhập hoặc mật khẩu không đúng!');
        }
    }
}

$page_title = 'Đăng nhập';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-laptop display-3 text-primary"></i>
                        <h2 class="mt-3">Đăng nhập</h2>
                        <p class="text-muted">Đăng nhập để tiếp tục mua sắm</p>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập hoặc Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Chưa có tài khoản? 
                                <a href="<?php echo base_url('index.php?page=register'); ?>">Đăng ký ngay</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Demo accounts -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Tài khoản demo:</h6>
                    <p class="mb-1"><strong>Admin:</strong> admin / admin123</p>
                    <p class="mb-0"><strong>User:</strong> user1 / admin123</p>
                </div>
            </div>
        </div>
    </div>
</div>
