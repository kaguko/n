<?php
/**
 * Header - Phần đầu trang web
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) . ' - ' : ''; ?>Laptop Store</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,.2);
        }
        
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        
        .price {
            color: var(--danger-color);
            font-size: 1.25rem;
            font-weight: bold;
        }
        
        .old-price {
            text-decoration: line-through;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
        
        .badge-featured {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
        }
        
        footer {
            margin-top: auto;
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
        }
        
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <?php 
    // Hiển thị flash message nếu có
    $flash = get_flash();
    if ($flash): 
    ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show" role="alert">
            <?php echo e($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
