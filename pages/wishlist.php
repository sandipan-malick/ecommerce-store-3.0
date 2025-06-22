<?php
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php?redirect=wishlist.php');
}

$user_id = getUserId();
$success_message = '';
$error_message = '';

// Handle add to cart action
if (isset($_GET['add_to_cart']) && is_numeric($_GET['add_to_cart'])) {
    $product_id = (int)$_GET['add_to_cart'];
    
    // Get product details
    $product_query = "SELECT * FROM products WHERE id = $product_id AND stock > 0";
    $product_result = mysqli_query($conn, $product_query);
    
    if (mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);
        
        // Add to cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $item_exists = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $_SESSION['cart'][$key]['quantity'] += 1;
                $item_exists = true;
                break;
            }
        }
        
        if (!$item_exists) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'name' => $product['name'],
                'price' => $product['sale_price'] ?? $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
        
        // Remove from wishlist
        $remove_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
        mysqli_query($conn, $remove_query);
        
        $success_message = "Product added to cart and removed from wishlist";
    } else {
        $error_message = "Product not available or out of stock";
    }
}

// Handle remove from wishlist action
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    
    $remove_query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    if (mysqli_query($conn, $remove_query)) {
        $success_message = "Product removed from wishlist";
    } else {
        $error_message = "Failed to remove product from wishlist";
    }
}

// Get wishlist items
$wishlist_query = "SELECT w.id, p.id as product_id, p.name, p.description, p.price, p.sale_price, p.image, p.stock, c.name as category_name 
                  FROM wishlist w 
                  JOIN products p ON w.product_id = p.id 
                  JOIN categories c ON p.category_id = c.id 
                  WHERE w.user_id = $user_id 
                  ORDER BY w.created_at DESC";
$wishlist_result = mysqli_query($conn, $wishlist_query);

$page_title = "My Wishlist";
?>

<div class="container py-4">
    <h1 class="mb-4">My Wishlist</h1>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (mysqli_num_rows($wishlist_result) > 0): ?>
        <div class="row">
            <?php while ($item = mysqli_fetch_assoc($wishlist_result)): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card product-card h-100">
                        <?php if ($item['sale_price'] && $item['sale_price'] < $item['price']): ?>
                            <div class="product-badge bg-danger text-white">Sale</div>
                        <?php endif; ?>
                        
                        <a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $item['product_id']; ?>">
                            <img src="<?php echo SITE_URL; ?>/assets/images/products/<?php echo $item['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo $item['name']; ?>">
                        </a>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="product-category mb-2"><?php echo $item['category_name']; ?></div>
                            <h5 class="card-title">
                                <a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $item['product_id']; ?>">
                                    <?php echo $item['name']; ?>
                                </a>
                            </h5>
                            
                            <div class="product-price mb-3">
                                <?php if ($item['sale_price'] && $item['sale_price'] < $item['price']): ?>
                                    <span class="text-danger"><?php echo formatPrice($item['sale_price']); ?></span>
                                    <span class="text-muted text-decoration-line-through"><?php echo formatPrice($item['price']); ?></span>
                                <?php else: ?>
                                    <span><?php echo formatPrice($item['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($item['stock'] > 0): ?>
                                        <a href="?add_to_cart=<?php echo $item['product_id']; ?>" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-times-circle me-2"></i>Out of Stock
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="?remove=<?php echo $item['product_id']; ?>" class="btn btn-outline-danger" title="Remove from Wishlist">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-heart-broken fa-5x text-muted"></i>
            </div>
            <h3>Your wishlist is empty</h3>
            <p class="text-muted">Browse our products and add items to your wishlist</p>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-primary mt-3">
                <i class="fas fa-shopping-bag me-2"></i>Browse Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>