<?php
/**
 * Database Configuration
 * Kết nối đến MySQL database
 */

// Thông tin kết nối database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'laptop_store');

// Tạo kết nối
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    
    // Set charset UTF-8
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
}

/**
 * Hàm thực thi query an toàn với prepared statement
 */
function db_query($sql, $params = []) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    $stmt->execute();
    return $stmt;
}

/**
 * Hàm lấy một dòng kết quả
 */
function db_fetch_one($sql, $params = []) {
    $stmt = db_query($sql, $params);
    if (!$stmt) return null;
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Hàm lấy nhiều dòng kết quả
 */
function db_fetch_all($sql, $params = []) {
    $stmt = db_query($sql, $params);
    if (!$stmt) return [];
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Hàm insert và trả về ID vừa insert
 */
function db_insert($sql, $params = []) {
    global $conn;
    $stmt = db_query($sql, $params);
    
    if (!$stmt) return false;
    
    return $conn->insert_id;
}
