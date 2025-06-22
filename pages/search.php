<?php
require_once '../config/config.php';
$page_title = "Search Results";

// Get and clean the search query
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if (!empty($search_query)) {
    // Make search case-insensitive
    $search_term = '%' . strtolower($search_query) . '%';

    // SQL query with LOWER() for case-insensitive matching
    $sql = "SELECT * FROM products WHERE LOWER(name) LIKE ? OR LOWER(description) LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $search_term, $search_term);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>

    <?php if (empty($products)): ?>
        <div class="alert alert-warning">No products found matching your search.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo IMAGES_URL . '/' . $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text fw-bold"><?php echo formatPrice($product['price']); ?></p>
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">View Product</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
