<?php
$page_title = "All Products";
require_once '../includes/header.php';

// Initialize variables for filtering and pagination
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Build the query based on filters
$query = "SELECT p.*, c.name as category_name FROM products p 
          JOIN categories c ON p.category_id = c.id";

// Add category filter if specified
if ($category_id > 0) {
    $query .= " WHERE p.category_id = $category_id";
}

// Add sorting
switch ($sort_by) {
    case 'price_low_high':
        $query .= " ORDER BY p.discount_price IS NULL, COALESCE(p.discount_price, p.price) ASC";
        break;
    case 'price_high_low':
        $query .= " ORDER BY p.discount_price IS NULL, COALESCE(p.discount_price, p.price) DESC";
        break;
    case 'newest':
        $query .= " ORDER BY p.created_at DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY p.name ASC";
        break;
    default:
        $query .= " ORDER BY p.id DESC";
}

// Add pagination
$query .= " LIMIT $offset, $items_per_page";

// Execute the query
$result = mysqli_query($conn, $query);

// Get total products count for pagination
$count_query = "SELECT COUNT(*) as total FROM products";
if ($category_id > 0) {
    $count_query .= " WHERE category_id = $category_id";
}
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_products = $count_row['total'];
$total_pages = ceil($total_products / $items_per_page);

// Get all categories for filter sidebar
$categories_query = "SELECT * FROM categories WHERE parent_id IS NULL";
$categories_result = mysqli_query($conn, $categories_query);

// Get subcategories
$subcategories_query = "SELECT * FROM categories WHERE parent_id IS NOT NULL";
$subcategories_result = mysqli_query($conn, $subcategories_query);
$subcategories = [];
while ($subcat = mysqli_fetch_assoc($subcategories_result)) {
    $subcategories[$subcat['parent_id']][] = $subcat;
}
?>

<div class="container py-4">
    <div class="row">
        <!-- Filter Sidebar -->
        <div class="col-md-3">
            <div class="filter-sidebar">
                <h4>Categories</h4>
                <div class="list-group mb-4">
                    <a href="products.php" class="list-group-item list-group-item-action <?php echo $category_id == 0 ? 'active' : ''; ?>">All Products</a>
                    <?php while ($category = mysqli_fetch_assoc($categories_result)): ?>
                        <a href="products.php?category=<?php echo $category['id']; ?>" class="list-group-item list-group-item-action <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                            <?php echo $category['name']; ?>
                        </a>
                        <?php if (isset($subcategories[$category['id']])): ?>
                            <?php foreach ($subcategories[$category['id']] as $subcat): ?>
                                <a href="products.php?category=<?php echo $subcat['id']; ?>" class="list-group-item list-group-item-action ps-4 <?php echo $category_id == $subcat['id'] ? 'active' : ''; ?>">
                                    - <?php echo $subcat['name']; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>
                
                <h4>Price Range</h4>
                <div class="mb-4">
                    <input type="range" class="form-range" id="price-range" min="0" max="50000" step="1000" value="50000">
                    <div class="d-flex justify-content-between">
                        <span>₹0</span>
                        <span id="price-value">₹50000</span>
                    </div>
                </div>
                
                <button id="apply-filters" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Products</h2>
                <div class="d-flex align-items-center">
                    <label for="sort-products" class="me-2">Sort by:</label>
                    <select id="sort-products" class="form-select">
                        <option value="default" <?php echo $sort_by == 'default' ? 'selected' : ''; ?>>Default</option>
                        <option value="price_low_high" <?php echo $sort_by == 'price_low_high' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high_low" <?php echo $sort_by == 'price_high_low' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="name_asc" <?php echo $sort_by == 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                    </select>
                </div>
            </div>
            
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="row products-container">
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <?php 
                        $image_url = !empty($product['image']) ? IMAGES_URL . '/products/' . $product['image'] : 'https://via.placeholder.com/300x300?text=' . $product['name'];
                        $price_display = !empty($product['discount_price']) ? 
                            '<span class="price discount-price">' . formatPrice($product['discount_price']) . '</span>' . 
                            '<span class="original-price">' . formatPrice($product['price']) . '</span>' : 
                            '<span class="price">' . formatPrice($product['price']) . '</span>';
                        $product_price = !empty($product['discount_price']) ? $product['discount_price'] : $product['price'];
                        ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card product-card" data-price="<?php echo $product_price; ?>">
                                <span class="category-badge"><?php echo $product['category_name']; ?></span>
                                <img src="<?php echo $image_url; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                    <div class="price-section">
                                        <?php echo $price_display; ?>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <button class="btn btn-sm btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $sort_by != 'default' ? '&sort='.$sort_by : ''; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $sort_by != 'default' ? '&sort='.$sort_by : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $sort_by != 'default' ? '&sort='.$sort_by : ''; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>No products found</h3>
                    <p>We couldn't find any products matching your criteria.</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alert Container for AJAX messages -->
<div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050;"></div>

<script>
$(document).ready(function() {
    // Handle sort change
    $('#sort-products').on('change', function() {
        var sortBy = $(this).val();
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('sort', sortBy);
        window.location.href = currentUrl.toString();
    });
    
    // Handle apply filters button
    $('#apply-filters').on('click', function() {
        var priceRange = $('#price-range').val();
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('price', priceRange);
        window.location.href = currentUrl.toString();
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>