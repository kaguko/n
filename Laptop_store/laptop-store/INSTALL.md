# HƯỚNG DẪN CÀI ĐẶT NHANH

## Bước 1: Chuẩn bị môi trường
- Cài đặt XAMPP/WAMP/LAMP
- Khởi động Apache và MySQL

## Bước 2: Setup Database
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Tạo database mới tên: `laptop_store`
3. Click vào database vừa tạo
4. Click tab "Import"
5. Chọn file `database.sql`
6. Click "Go" để import

## Bước 3: Cấu hình
1. Mở file `config/database.php`
2. Kiểm tra thông tin kết nối:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Mật khẩu MySQL của bạn
   define('DB_NAME', 'laptop_store');
   ```

## Bước 4: Chạy website
1. Copy thư mục `laptop-store` vào:
   - XAMPP: `C:\xampp\htdocs\`
   - WAMP: `C:\wamp64\www\`
   - Linux: `/var/www/html/`

2. Mở trình duyệt và truy cập:
   - Frontend: http://localhost/laptop-store/
   - Admin: http://localhost/laptop-store/admin/

## Bước 5: Đăng nhập
### Tài khoản Admin:
- Username: `admin`
- Password: `admin123`

### Tài khoản User:
- Username: `user1`
- Password: `admin123`

## Lưu ý quan trọng:
- Đảm bảo thư mục `assets/uploads/` có quyền ghi (chmod 777 trên Linux)
- Nếu gặp lỗi, bật display_errors trong PHP để xem chi tiết lỗi
- Kiểm tra PHP version >= 7.4

## Xử lý lỗi thường gặp:

### Lỗi "Access denied for user"
→ Sai thông tin database, check lại config/database.php

### Lỗi "Call to undefined function mysqli_connect()"
→ Bật extension mysqli trong php.ini

### Không upload được ảnh
→ Cấp quyền cho thư mục uploads:
```bash
chmod -R 777 assets/uploads/
```

### Lỗi 404 Not Found
→ Kiểm tra đường dẫn trong .htaccess:
```
RewriteBase /laptop-store/
```

## Cấu trúc URL:
- Trang chủ: `index.php`
- Sản phẩm: `index.php?page=products`
- Chi tiết: `index.php?page=product-detail&id=1`
- Giỏ hàng: `index.php?page=cart`
- Admin: `admin/index.php`

---

**Chúc bạn thành công!** 🎉

Nếu cần hỗ trợ, hãy kiểm tra file README.md để biết thêm chi tiết.
