<?php
require_once '../config/config.php';
require_once '../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Product Details";
require_once '../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Invalid product ID.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$product_id = (int)$_GET['id'];

// Fetch product and category name
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.id = $product_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Product not found.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

$product = mysqli_fetch_assoc($result);
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <img src="<?= IMAGES_URL . '/' . htmlspecialchars($product['image']) ?>" class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p>
                <?php if ($product['discount_price']): ?>
                    <span class="text-muted text-decoration-line-through">₹<?= number_format($product['price'], 2) ?></span>
                    <span class="text-success fw-bold">₹<?= number_format($product['discount_price'], 2) ?></span>
                <?php else: ?>
                    <span class="fw-bold">₹<?= number_format($product['price'], 2) ?></span>
                <?php endif; ?>
            </p>
            <form method="post" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control" style="width: 100px;">
                </div>
                <button type="submit" class="btn btn-primary">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
