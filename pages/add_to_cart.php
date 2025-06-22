<?php
require_once '../config/config.php';

// Enable full error reporting (for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if request is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int) $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($quantity < 1) {
        $quantity = 1;
    }

    // Check if product exists
    $stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    if (!$stmt) {
        die("DB prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $product = mysqli_fetch_assoc($result);

        // If user is logged in, store in DB
        if (isLoggedIn()) {
            $user_id = getUserId();

            // Check if item already exists in cart
            $check_stmt = mysqli_prepare($conn, "SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            mysqli_stmt_bind_param($check_stmt, 'ii', $user_id, $product_id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);

            if ($check_result && mysqli_num_rows($check_result) > 0) {
                // Update quantity
                $update_stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                mysqli_stmt_bind_param($update_stmt, 'iii', $quantity, $user_id, $product_id);
                mysqli_stmt_execute($update_stmt);
            } else {
                // Insert new record
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($insert_stmt, 'iii', $user_id, $product_id, $quantity);
                mysqli_stmt_execute($insert_stmt);
            }

        } else {
            // Guest user - use session cart
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'product_id' => $product_id,
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity
                ];
            }
        }

        // Redirect to cart or product list
        $_SESSION['success'] = "Product added to cart successfully.";
        header("Location: " . SITE_URL . "/pages/cart.php");
        exit;

    } else {
        $_SESSION['error'] = "Product not found.";
        header("Location: " . SITE_URL . "/pages/products.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: " . SITE_URL . "/pages/products.php");
    exit;
}
