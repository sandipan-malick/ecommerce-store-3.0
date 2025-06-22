<?php
// Site configuration
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/ecommerce-store-3.0');
if (!defined('SITE_NAME')) define('SITE_NAME', 'E-Commerce Store 3.0');

// Path configuration
if (!defined('ROOT_PATH')) define('ROOT_PATH', dirname(__DIR__));
if (!defined('ASSETS_URL')) define('ASSETS_URL', SITE_URL . '/assets');
if (!defined('IMAGES_URL')) define('IMAGES_URL', ASSETS_URL . '/images');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once 'db.php';

// Helper functions
function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUsername() {
    return $_SESSION['username'] ?? null;
}

function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

function getCartCount() {
    global $conn;
    $count = 0;

    if (isLoggedIn()) {
        $user_id = getUserId();
        $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['total'] ?? 0;
        }
    } elseif (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }

    return $count;
}
?>
