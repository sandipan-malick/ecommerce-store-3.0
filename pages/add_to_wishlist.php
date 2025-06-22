<?php
require_once '../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $response = [
        'success' => false,
        'message' => 'Please login to add items to your wishlist',
        'redirect' => SITE_URL . '/pages/login.php'
    ];
    echo json_encode($response);
    exit;
}

// Check if request is AJAX
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Get user ID and product ID
    $user_id = getUserId();
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    // Validate product ID
    if ($product_id <= 0) {
        $response = [
            'success' => false,
            'message' => 'Invalid product ID'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Check if product exists
    $product_query = "SELECT id FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    
    if (mysqli_num_rows($product_result) == 0) {
        $response = [
            'success' => false,
            'message' => 'Product not found'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Check if product is already in wishlist
    $check_query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Product already in wishlist, remove it (toggle functionality)
        $delete_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        
        if (mysqli_query($conn, $delete_query)) {
            $response = [
                'success' => true,
                'message' => 'Product removed from wishlist',
                'action' => 'removed'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to remove product from wishlist: ' . mysqli_error($conn)
            ];
        }
    } else {
        // Add product to wishlist
        $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
        
        if (mysqli_query($conn, $insert_query)) {
            $response = [
                'success' => true,
                'message' => 'Product added to wishlist',
                'action' => 'added'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to add product to wishlist: ' . mysqli_error($conn)
            ];
        }
    }
    
    echo json_encode($response);
} else {
    // Not an AJAX request
    header('HTTP/1.1 403 Forbidden');
    echo 'Access forbidden';
}
?>