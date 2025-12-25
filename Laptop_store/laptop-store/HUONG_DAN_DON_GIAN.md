# HƯỚNG DẪN LÀM VIỆC ĐƠN GIẢN - LAPTOP STORE

## 🎯 QUY TẮC VÀNG - CHỈ CẦN NHỚ 3 ĐIỀU

1. ❌ **KHÔNG BAO GIỜ** làm việc trực tiếp trên nhánh `main`
2. ✅ **LUÔN LUÔN** tạo nhánh mới để làm việc
3. ✅ **LUÔN LUÔN** commit code mỗi ngày

---

## 👥 AI LÀM GÌ?

### **Bạn A - Làm Giao Diện (Frontend)**
- Làm các file `.php` trong `pages/` và `includes/`
- Làm file CSS trong `assets/css/`
- Làm JavaScript trong `assets/js/`
- **Đặt tên nhánh:** `giaodien-trangchu`, `giaodien-giohang`, v.v.

### **Bạn B - Làm Backend (Xử lý dữ liệu)**
- Làm các file trong `config/`
- Làm logic PHP (xử lý form, database)
- Làm file `database.sql`
- **Đặt tên nhánh:** `backend-dangnhap`, `backend-giohang`, v.v.

### **Bạn C - Trưởng Nhóm (Quản lý)**
- Kiểm tra code của A và B
- Gộp code vào project
- Sửa lỗi cuối cùng
- Viết tài liệu

---

## 📅 KẾ HOẠCH LÀM VIỆC 6 TUẦN

### **TUẦN 1: CÀI ĐẶT & ĐĂNG NHẬP**

#### **Ngày 1-2: Cài đặt dự án (Bạn C làm)**
```bash
# Không cần làm gì, chỉ tạo folder và push code lên GitHub
```

**Việc cần làm:**
- [ ] Tạo repository trên GitHub
- [ ] Tải code lên GitHub
- [ ] Mời A và B vào repository

---

#### **Ngày 3-4: Làm Database (Bạn B làm)**

**Bước 1:** Mở Git Bash, gõ:
```bash
git checkout -b backend-database
```

**Bước 2:** Làm file `database.sql` (tạo bảng users, products, orders...)

**Bước 3:** Mỗi khi làm xong 1 việc nhỏ, gõ:
```bash
git add .
git commit -m "them bang users"
```

**Bước 4:** Cuối ngày, gõ:
```bash
git push origin backend-database
```

**Bước 5:** Lên GitHub, tạo Pull Request để Bạn C kiểm tra

---

#### **Ngày 5-7: Làm Đăng nhập/Đăng ký**

**Bạn B làm Backend:**

```bash
# Ngày 5: Tạo nhánh mới
git checkout -b backend-dangnhap

# Làm file login.php, register.php, logout.php
# Mỗi lần làm xong 1 file, commit:
git add .
git commit -m "them chuc nang dang nhap"

# Cuối ngày push
git push origin backend-dangnhap
```

**Bạn A làm Giao diện:**

```bash
# Ngày 5: Tạo nhánh mới
git checkout -b giaodien-dangnhap

# Làm form đăng nhập đẹp với Bootstrap
# Làm form đăng ký
# Mỗi lần làm xong, commit:
git add .
git commit -m "lam form dang nhap"

# Cuối ngày push
git push origin giaodien-dangnhap
```

**Bạn C:** Kiểm tra và gộp code của A và B

---

### **TUẦN 2: QUẢN LÝ SẢN PHẨM**

#### **Ngày 8-10: Backend Sản phẩm (Bạn B)**

```bash
# Tạo nhánh mới
git checkout -b backend-sanpham

# Làm functions.php để thêm/sửa/xóa sản phẩm
git add .
git commit -m "them chuc nang them san pham"

git add .
git commit -m "them chuc nang sua san pham"

git add .
git commit -m "them chuc nang xoa san pham"

# Push lên
git push origin backend-sanpham
```

---

#### **Ngày 11-14: Giao diện Sản phẩm (Bạn A)**

```bash
# Tạo nhánh mới
git checkout -b giaodien-sanpham

# Ngày 11: Làm trang danh sách sản phẩm
git add .
git commit -m "lam trang danh sach san pham"

# Ngày 12: Làm trang chi tiết sản phẩm
git add .
git commit -m "lam trang chi tiet san pham"

# Ngày 13: Làm trang admin quản lý sản phẩm
git add .
git commit -m "lam trang admin san pham"

# Push lên
git push origin giaodien-sanpham
```

---

### **TUẦN 3: GIỎ HÀNG & THANH TOÁN**

#### **Ngày 15-17: Backend Giỏ hàng (Bạn B)**

```bash
git checkout -b backend-giohang

# Ngày 15: Làm thêm vào giỏ
git add .
git commit -m "them vao gio hang"

# Ngày 16: Làm cập nhật số lượng
git add .
git commit -m "cap nhat so luong gio hang"

# Ngày 17: Làm thanh toán
git add .
git commit -m "xu ly thanh toan"

git push origin backend-giohang
```

---

#### **Ngày 18-21: Giao diện Giỏ hàng (Bạn A)**

```bash
git checkout -b giaodien-giohang

# Ngày 18: Làm trang giỏ hàng
git add .
git commit -m "lam trang gio hang"

# Ngày 19: Làm trang thanh toán
git add .
git commit -m "lam trang thanh toan"

# Ngày 20: Làm trang thành công
git add .
git commit -m "lam trang thanh toan thanh cong"

git push origin giaodien-giohang
```

---

### **TUẦN 4: TRANG CHỦ**

#### **Ngày 22-24: Backend Trang chủ (Bạn B)**

```bash
git checkout -b backend-trangchu

# Lấy sản phẩm nổi bật, mới nhất
git add .
git commit -m "lay san pham noi bat"

git push origin backend-trangchu
```

---

#### **Ngày 25-28: Giao diện Trang chủ (Bạn A)**

```bash
git checkout -b giaodien-trangchu

# Ngày 25: Làm slider đẹp
git add .
git commit -m "lam slider trang chu"

# Ngày 26: Làm phần sản phẩm nổi bật
git add .
git commit -m "lam phan san pham noi bat"

# Ngày 27: Làm phần danh mục
git add .
git commit -m "lam phan danh muc"

git push origin giaodien-trangchu
```

---

### **TUẦN 5: SỬA LỖI & LÀM ĐẸP**

#### **Ngày 29-35: Cả nhóm làm**

**Bạn C (Trưởng nhóm):**
- Test toàn bộ website
- Tạo danh sách lỗi cần sửa
- Phân công A và B sửa

**Bạn A & B:**
- Sửa lỗi theo danh sách
- Làm đẹp giao diện hơn
- Commit mỗi khi sửa xong 1 lỗi

```bash
git add .
git commit -m "sua loi gio hang khong cap nhat"
```

---

### **TUẦN 6: HOÀN THIỆN**

#### **Ngày 36-42: Cả nhóm**

**Bạn C:**
- Viết tài liệu hướng dẫn
- Chuẩn bị báo cáo

**Bạn A & B:**
- Test lại toàn bộ
- Sửa lỗi nhỏ cuối cùng

---

## 📝 CÁCH COMMIT ĐƠN GIẢN

### **Bạn chỉ cần nhớ 5 từ khóa:**

1. **`them`** - Khi thêm tính năng mới
   ```bash
   git commit -m "them chuc nang dang nhap"
   git commit -m "them gio hang"
   git commit -m "them tim kiem"
   ```

2. **`sua`** - Khi sửa lỗi
   ```bash
   git commit -m "sua loi dang nhap"
   git commit -m "sua loi gio hang khong hien thi"
   ```

3. **`lam`** - Khi làm giao diện
   ```bash
   git commit -m "lam trang chu"
   git commit -m "lam form dang nhap"
   ```

4. **`cap nhat`** - Khi cập nhật code cũ
   ```bash
   git commit -m "cap nhat giao dien gio hang"
   git commit -m "cap nhat ham tinh tien"
   ```

5. **`xoa`** - Khi xóa code không dùng
   ```bash
   git commit -m "xoa code cu"
   ```

---

## 🔄 QUY TRÌNH LÀM VIỆC HÀNG NGÀY

### **SÁNG (Bắt đầu làm việc):**

```bash
# Bước 1: Mở Git Bash trong folder dự án
cd C:/wamp64/www/Laptop_store/-n-/laptop-store

# Bước 2: Tạo nhánh mới để làm việc
git checkout -b ten-nhanh-cua-ban
# Ví dụ: git checkout -b giaodien-trangchu
```

### **TRONG NGÀY (Mỗi khi làm xong 1 việc nhỏ):**

```bash
# Bước 1: Lưu code
git add .

# Bước 2: Commit với message đơn giản
git commit -m "them form dang nhap"
```

### **CHIỀU (Kết thúc làm việc):**

```bash
# Push code lên GitHub
git push origin ten-nhanh-cua-ban
# Ví dụ: git push origin giaodien-trangchu
```

### **KHI XONG HẾT CÔNG VIỆC:**

1. Vào GitHub
2. Nhấn nút **"New Pull Request"**
3. Chọn nhánh của bạn
4. Viết mô tả ngắn: "Đã làm xong giao diện trang chủ"
5. Nhấn **"Create Pull Request"**
6. Tag Bạn C để kiểm tra

---

## ⚠️ KHI GẶP LỖI

### **Lỗi 1: "Cannot push" hoặc "rejected"**

**Giải pháp:**
```bash
git pull origin develop
# Nếu có xung đột, nhờ Bạn C giúp
```

---

### **Lỗi 2: "Already exists" khi tạo nhánh**

**Giải pháp:**
```bash
# Đổi sang nhánh đó
git checkout ten-nhanh-da-ton-tai
```

---

### **Lỗi 3: Quên commit nhưng đã sửa nhiều file**

**Giải pháp:**
```bash
# Commit hết luôn
git add .
git commit -m "cap nhat nhieu file"
```

---

### **Lỗi 4: Làm nhầm nhánh**

**Giải pháp:**
```bash
# Hủy thay đổi chưa commit
git checkout .

# Chuyển sang nhánh đúng
git checkout ten-nhanh-dung
```

---

## 📌 CHECKLIST TRƯỚC KHI PUSH CODE

### **Bạn A (Frontend) - Checklist:**
- [ ] Code chạy được không lỗi?
- [ ] Giao diện đẹp trên điện thoại?
- [ ] Giao diện đẹp trên máy tính?
- [ ] Nút bấm có hoạt động?
- [ ] Form có validate input?

### **Bạn B (Backend) - Checklist:**
- [ ] Code chạy được không lỗi?
- [ ] Database có data test?
- [ ] Function có return đúng?
- [ ] Có validate input từ user?
- [ ] Không có SQL injection?

--

## 🎓 TÀI LIỆU HỌC THÊM

### **Git cơ bản:**
- `git checkout -b tên-nhánh` = Tạo nhánh mới
- `git add .` = Thêm tất cả file vào commit
- `git commit -m "message"` = Lưu thay đổi
- `git push origin tên-nhánh` = Đẩy code lên GitHub



## ✅ QUY TẮC ĐƠN GIẢN NHẤT

1. **Mỗi ngày:**
   - Tạo nhánh mới (hoặc dùng nhánh cũ)
   - Làm việc
   - Commit nhiều lần trong ngày
   - Push lên GitHub cuối ngày

2. **Mỗi tuần:**
   - Tạo Pull Request khi xong việc
   - Chờ Bạn C review
   - Sửa nếu Bạn C yêu cầu

3. **Không được:**
   - ❌ Làm việc trên nhánh `main`
   - ❌ Push code không chạy được
   - ❌ Copy code từ internet mà không hiểu








