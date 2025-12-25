# LAPTOP STORE - WEBSITE BÁN LAPTOP

Website bán laptop hoàn chỉnh được xây dựng bằng PHP thuần (không framework) và Bootstrap 5.

## 🚀 TÍNH NĂNG

### Frontend (Khách hàng)
- ✅ Trang chủ với slider và sản phẩm nổi bật
- ✅ Danh sách sản phẩm với filter (giá, hãng, RAM, CPU)
- ✅ Chi tiết sản phẩm với thông số kỹ thuật đầy đủ
- ✅ Giỏ hàng (thêm, xóa, cập nhật số lượng)
- ✅ Thanh toán đơn giản
- ✅ Đăng ký / Đăng nhập
- ✅ Tìm kiếm sản phẩm
- ✅ Responsive design (Mobile, Tablet, Desktop)

### Backend (Admin)
- ✅ Dashboard với thống kê
- ✅ Quản lý sản phẩm (CRUD)
- ✅ Quản lý đơn hàng
- ✅ Quản lý người dùng
- ✅ Quản lý danh mục & hãng

## 💻 TECH STACK

- **Backend**: PHP thuần (không framework)
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **Database**: MySQL
- **Authentication**: Session-based
- **Security**: Password hashing, Prepared statements, XSS prevention

## 📋 YÊU CẦU HỆ THỐNG

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Apache/Nginx web server
- Extension: mysqli, gd (cho xử lý ảnh)

## 🔧 HƯỚNG DẪN CÀI ĐẶT

### Bước 1: Clone/Download dự án
```bash
# Nếu dùng Git
git clone <repository-url>

# Hoặc download và giải nén vào thư mục web root
# Ví dụ: C:\xampp\htdocs\laptop-store (Windows)
# Hoặc: /var/www/html/laptop-store (Linux)
```

### Bước 2: Tạo Database
1. Mở phpMyAdmin hoặc MySQL client
2. Tạo database mới tên `laptop_store`
3. Import file `database.sql`:
   ```sql
   # Trong MySQL console
   mysql -u root -p laptop_store < database.sql
   
   # Hoặc dùng phpMyAdmin: Import > Chọn file database.sql
   ```

### Bước 3: Cấu hình Database
Mở file `config/database.php` và chỉnh sửa thông tin kết nối:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Thay đổi nếu cần
define('DB_PASS', '');              // Thay đổi nếu cần
define('DB_NAME', 'laptop_store');
```

### Bước 4: Cấu hình quyền thư mục
```bash
# Linux/Mac
chmod 755 -R laptop-store/
chmod 777 -R laptop-store/assets/uploads/

# Windows: Click chuột phải > Properties > Security
# Cho phép Full Control cho thư mục uploads
```

### Bước 5: Truy cập website
```
Frontend: http://localhost/laptop-store/
Admin:    http://localhost/laptop-store/admin/
```

## 👤 TÀI KHOẢN DEMO

### Tài khoản Admin
- **Username**: admin
- **Password**: admin123
- **URL**: http://localhost/laptop-store/admin/

### Tài khoản User
- **Username**: user1
- **Password**: admin123

## 📁 CẤU TRÚC THƯ MỤC

```
laptop-store/
├── admin/                      # Admin panel
│   ├── includes/              # Header/Footer admin
│   ├── index.php              # Dashboard
│   ├── products.php           # Quản lý sản phẩm
│   ├── orders.php             # Quản lý đơn hàng
│   └── users.php              # Quản lý người dùng
├── assets/                     # Tài nguyên tĩnh
│   ├── css/                   # CSS files
│   ├── js/                    # JavaScript files
│   └── uploads/               # Ảnh sản phẩm upload
│       └── products/
├── config/                     # Cấu hình
│   ├── database.php           # Kết nối database
│   └── functions.php          # Hàm tiện ích
├── includes/                   # Template parts
│   ├── header.php             # Header chung
│   ├── footer.php             # Footer chung
│   └── navigation.php         # Menu điều hướng
├── pages/                      # Các trang frontend
│   ├── home.php               # Trang chủ
│   ├── products.php           # Danh sách sản phẩm
│   ├── product-detail.php     # Chi tiết sản phẩm
│   ├── cart.php               # Giỏ hàng
│   ├── checkout.php           # Thanh toán
│   ├── login.php              # Đăng nhập
│   ├── register.php           # Đăng ký
│   └── ...
├── database.sql               # File SQL tạo database
├── index.php                  # File chính (router)
└── README.md                  # Tài liệu này
```

## 🎨 SCREENSHOTS

### Trang chủ
- Hero slider với 3 slides
- Features section (Giao hàng miễn phí, Bảo hành, Đổi trả...)
- Sản phẩm nổi bật
- Danh mục sản phẩm
- Sản phẩm mới nhất

### Danh sách sản phẩm
- Filter theo: Danh mục, Hãng, Giá, RAM
- Sắp xếp: Mới nhất, Giá, Tên
- Pagination
- Responsive grid

### Chi tiết sản phẩm
- Ảnh sản phẩm lớn
- Thông tin chi tiết
- Thông số kỹ thuật
- Sản phẩm liên quan

### Admin Dashboard
- Thống kê tổng quan
- Đơn hàng gần đây
- Sản phẩm bán chạy

## 🔒 BẢO MẬT

- ✅ Password hashing với `password_hash()` (bcrypt)
- ✅ Prepared statements chống SQL injection
- ✅ XSS prevention với `htmlspecialchars()`
- ✅ Session management
- ✅ CSRF protection (có thể thêm token)
- ✅ File upload validation

## 📝 HƯỚNG DẪN SỬ DỤNG

### Quản lý sản phẩm
1. Đăng nhập admin
2. Vào menu "Sản phẩm"
3. Click "Thêm sản phẩm mới"
4. Điền thông tin và upload ảnh
5. Click "Lưu"

### Xử lý đơn hàng
1. Vào menu "Đơn hàng"
2. Click vào mã đơn hàng để xem chi tiết
3. Cập nhật trạng thái: Pending → Processing → Shipped → Delivered
4. Khách hàng sẽ nhận thông báo (nếu có email)

### Thêm danh mục mới
- Vào menu "Danh mục" hoặc "Hãng"
- Thêm mới hoặc chỉnh sửa

## 🚀 TRIỂN KHAI

### Triển khai lên hosting
1. Upload toàn bộ file lên hosting
2. Tạo database và import file SQL
3. Cập nhật thông tin database trong `config/database.php`
4. Cấu hình URL trong hàm `base_url()` nếu cần
5. Đảm bảo thư mục `assets/uploads/` có quyền ghi

### Cải thiện performance
- Bật Gzip compression
- Enable caching
- Optimize images
- Sử dụng CDN cho Bootstrap

## 🐛 XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi kết nối database
```
Kết nối thất bại: Access denied for user...
```
→ Kiểm tra lại thông tin DB_USER, DB_PASS trong config/database.php

### Lỗi upload ảnh
```
Upload file thất bại
```
→ Kiểm tra quyền thư mục assets/uploads/ (chmod 777)

### Trang trắng (White screen)
→ Bật display_errors trong php.ini hoặc thêm vào đầu file:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📚 TÀI LIỆU THAM KHẢO

- [PHP Documentation](https://www.php.net/docs.php)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.3/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 📞 HỖ TRỢ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra lại hướng dẫn cài đặt
2. Xem phần "Xử lý lỗi thường gặp"
3. Kiểm tra PHP error log

## 📄 LICENSE

MIT License - Tự do sử dụng cho mục đích học tập và thương mại.

## ✨ CREDITS

Developed with ❤️ using PHP & Bootstrap 5

---

**Note**: Đây là project học tập. Nên bổ sung thêm các tính năng bảo mật và tối ưu hóa trước khi sử dụng trong môi trường production.
