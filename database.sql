-- ===================================
-- DATABASE: LAPTOP STORE
-- Tạo database và các bảng cần thiết
-- ===================================

CREATE DATABASE IF NOT EXISTS laptop_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE laptop_store;

-- ===================================
-- Bảng Users (Người dùng)
-- ===================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Categories (Danh mục)
-- ===================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Brands (Hãng)
-- ===================================
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Products (Sản phẩm)
-- ===================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    category_id INT,
    brand_id INT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    cpu VARCHAR(100),
    ram VARCHAR(50),
    storage VARCHAR(100),
    screen VARCHAR(100),
    graphics VARCHAR(100),
    os VARCHAR(50),
    weight VARCHAR(50),
    image VARCHAR(255),
    description TEXT,
    specifications TEXT,
    stock INT DEFAULT 0,
    views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Orders (Đơn hàng)
-- ===================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Order Items (Chi tiết đơn hàng)
-- ===================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Cart (Giỏ hàng - Optional, dùng session cũng được)
-- ===================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- Bảng Reviews (Đánh giá - Optional)
-- ===================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===================================
-- INSERT DỮ LIỆU MẪU
-- ===================================

-- Admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@laptop.com', '$2y$10$7v5MqzYw5YN5UqR5qJ5oFuO5kzJ5YH5J5J5J5J5J5J5J5J5J5J5OG', 'Administrator', 'admin'),
('user1', 'user1@email.com', '$2y$10$7v5MqzYw5YN5UqR5qJ5oFuO5kzJ5YH5J5J5J5J5J5J5J5J5J5J5OG', 'Nguyễn Văn A', 'user');

-- Categories
INSERT INTO categories (name, slug, description) VALUES
('Gaming', 'gaming', 'Laptop Gaming hiệu năng cao'),
('Văn phòng', 'van-phong', 'Laptop cho công việc văn phòng'),
('Đồ họa', 'do-hoa', 'Laptop chuyên đồ họa, thiết kế'),
('Mỏng nhẹ', 'mong-nhe', 'Laptop mỏng nhẹ, di động'),
('Sinh viên', 'sinh-vien', 'Laptop phù hợp sinh viên');

-- Brands
INSERT INTO brands (name, slug) VALUES
('Dell', 'dell'),
('HP', 'hp'),
('Lenovo', 'lenovo'),
('Asus', 'asus'),
('Acer', 'acer'),
('MSI', 'msi'),
('Apple', 'apple'),
('LG', 'lg');

-- Products
INSERT INTO products (name, slug, category_id, brand_id, price, discount_price, cpu, ram, storage, screen, graphics, os, weight, image, description, stock, is_featured, status) VALUES
('Dell Gaming G15 5520', 'dell-gaming-g15-5520', 1, 1, 25990000, 23990000, 'Intel Core i7-12700H', '16GB DDR5', '512GB SSD NVMe', '15.6" FHD 120Hz', 'NVIDIA RTX 3060 6GB', 'Windows 11', '2.5kg', 'dell-g15.jpg', 'Laptop gaming Dell G15 với hiệu năng mạnh mẽ, màn hình 120Hz mượt mà, phù hợp cho game thủ và người làm đồ họa.', 15, TRUE, 'active'),

('HP Pavilion 15-eg2063TX', 'hp-pavilion-15-eg2063tx', 2, 2, 18990000, 17490000, 'Intel Core i5-1235U', '8GB DDR4', '512GB SSD', '15.6" FHD', 'NVIDIA MX550 2GB', 'Windows 11', '1.75kg', 'hp-pavilion.jpg', 'Laptop HP Pavilion thiết kế đẹp, cấu hình ổn định cho công việc văn phòng và giải trí.', 20, TRUE, 'active'),

('Lenovo ThinkPad X1 Carbon Gen 10', 'lenovo-thinkpad-x1-carbon-gen10', 2, 3, 42990000, NULL, 'Intel Core i7-1255U', '16GB LPDDR5', '512GB SSD', '14" WUXGA', 'Intel Iris Xe', 'Windows 11 Pro', '1.12kg', 'lenovo-x1.jpg', 'ThinkPad X1 Carbon - laptop doanh nhân cao cấp, siêu mỏng nhẹ, bền bỉ theo thời gian.', 8, TRUE, 'active'),

('Asus ROG Strix G15', 'asus-rog-strix-g15', 1, 4, 32990000, 29990000, 'AMD Ryzen 7 6800H', '16GB DDR5', '1TB SSD', '15.6" FHD 300Hz', 'NVIDIA RTX 3070 8GB', 'Windows 11', '2.3kg', 'asus-rog.jpg', 'Asus ROG Strix G15 - gaming laptop hàng đầu với màn hình 300Hz, hiệu năng đỉnh cao.', 10, TRUE, 'active'),

('Acer Aspire 5 A515-57-53S7', 'acer-aspire-5-a515', 5, 5, 13990000, 12990000, 'Intel Core i5-1235U', '8GB DDR4', '256GB SSD', '15.6" FHD', 'Intel Iris Xe', 'Windows 11', '1.7kg', 'acer-aspire.jpg', 'Laptop phổ thông cho sinh viên với giá cả hợp lý, cấu hình đáp ứng nhu cầu học tập.', 30, FALSE, 'active'),

('MSI Creator Z16P', 'msi-creator-z16p', 3, 6, 56990000, NULL, 'Intel Core i9-12900H', '32GB DDR5', '1TB SSD', '16" QHD+ 165Hz', 'NVIDIA RTX 3080 Ti 16GB', 'Windows 11 Pro', '2.39kg', 'msi-creator.jpg', 'MSI Creator Z16P - workstation laptop cho chuyên gia đồ họa, render video chuyên nghiệp.', 5, TRUE, 'active'),

('MacBook Air M2 2022', 'macbook-air-m2-2022', 4, 7, 32990000, 31490000, 'Apple M2 8-core', '16GB Unified', '512GB SSD', '13.6" Liquid Retina', 'Apple M2 10-core GPU', 'macOS', '1.24kg', 'macbook-air-m2.jpg', 'MacBook Air M2 - thiết kế sang trọng, hiệu năng ấn tượng, pin trâu dành cho người dùng Apple.', 12, TRUE, 'active'),

('LG Gram 17Z90Q', 'lg-gram-17z90q', 4, 8, 36990000, 34990000, 'Intel Core i7-1260P', '16GB LPDDR5', '512GB SSD', '17" WQXGA', 'Intel Iris Xe', 'Windows 11', '1.35kg', 'lg-gram.jpg', 'LG Gram 17 - laptop 17 inch siêu nhẹ chỉ 1.35kg, màn hình lớn lý tưởng cho làm việc đa nhiệm.', 7, FALSE, 'active'),

('Dell XPS 13 Plus 9320', 'dell-xps-13-plus-9320', 4, 1, 45990000, NULL, 'Intel Core i7-1280P', '16GB LPDDR5', '1TB SSD', '13.4" FHD+', 'Intel Iris Xe', 'Windows 11', '1.26kg', 'dell-xps13.jpg', 'Dell XPS 13 Plus - thiết kế premium, màn hình InfinityEdge tuyệt đẹp, hiệu năng mạnh mẽ.', 6, TRUE, 'active'),

('Asus Vivobook 15 OLED', 'asus-vivobook-15-oled', 5, 4, 16990000, 15490000, 'Intel Core i5-1135G7', '8GB DDR4', '512GB SSD', '15.6" FHD OLED', 'Intel Iris Xe', 'Windows 11', '1.8kg', 'asus-vivobook.jpg', 'Asus Vivobook với màn hình OLED sống động, giá tốt cho sinh viên và văn phòng.', 25, FALSE, 'active'),

('HP Omen 16-b1022TX', 'hp-omen-16-b1022tx', 1, 2, 38990000, 35990000, 'Intel Core i7-12700H', '16GB DDR5', '1TB SSD', '16.1" QHD 165Hz', 'NVIDIA RTX 3070 Ti 8GB', 'Windows 11', '2.3kg', 'hp-omen.jpg', 'HP Omen 16 - gaming laptop cao cấp với màn hình QHD sắc nét, âm thanh Bang & Olufsen.', 8, TRUE, 'active'),

('Lenovo IdeaPad Gaming 3', 'lenovo-ideapad-gaming-3', 1, 3, 19990000, 18490000, 'AMD Ryzen 5 5600H', '8GB DDR4', '512GB SSD', '15.6" FHD 120Hz', 'NVIDIA GTX 1650 4GB', 'Windows 11', '2.25kg', 'lenovo-gaming3.jpg', 'Lenovo IdeaPad Gaming 3 - lựa chọn tốt cho game thủ nhập môn với mức giá phải chăng.', 18, FALSE, 'active');

