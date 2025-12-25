<?php
/**
 * Logout - Đăng xuất
 */

// Xóa session
session_destroy();

set_flash('success', 'Đăng xuất thành công!');
redirect(base_url('index.php'));
