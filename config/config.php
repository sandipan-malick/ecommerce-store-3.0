<?php
// Site configuration
define('SITE_URL', 'http://localhost/ecommerce-store-3.0');
define('SITE_NAME', 'E-Commerce Store 3.0');

// Path configuration
define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_URL', SITE_URL . '/assets');
define('IMAGES_URL', ASSETS_URL . '/images');

// Start session once globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once 'db.php';

// Load helper functions
require_once 'functions.php';
?>
