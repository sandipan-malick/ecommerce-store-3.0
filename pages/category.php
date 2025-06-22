<?php
require_once '../includes/header.php';

// Check if category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(SITE_URL . '/pages/products.php');
}

$category_id = (int)$_GET['id'];

// Fetch category details
$category_query = "SELECT * FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_query);

if (mysqli_num_rows($category_result) == 0) {
    // Category not found
    redirect(SITE_URL . '/pages/products.php');
}

$category = mysqli_fetch_assoc($category_result);
$page_title = $category['name'];

// Get parent category if this is a subcategory
$parent_category = null;
if ($category['parent_id']) {
    $parent_query = "SELECT * FROM categories WHERE id = {$category['parent_id']}";
    $parent_result = mysqli_query($conn, $parent_query);
    if (mysqli_num_rows($parent_result) > 0) {
        $parent_category = mysqli_fetch_assoc($parent_result);
    }
}

// Get subcategories if this is a parent category
$subcategories = [];
$subcategory_query = "SELECT * FROM categories WHERE parent_id = $category_id";
$subcategory_result = mysqli_query($conn, $subcategory_query);
if (mysqli_num_rows($subcategory_result) > 0) {
    while ($row = mysqli_fetch_assoc($subcategory_result)) {
        $subcategories[] = $row;
    }
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Products per page
$offset = ($page - 1) * $limit;

// Sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$order_by = 'p.created_at DESC'; // Default sorting

switch ($sort) {
    case 'price_low':
        $order_by = 'COALESCE(p.discount_price, p.price) ASC';
        break;
    case 'price_high':
        $order_by = 'COALESCE(p.discount_price, p.price) DESC';
        break;
    case 'name_asc':
        $order_by = 'p.name ASC';
        break;
    case 'popularity':
        $order_by = 'p.sales_count DESC';
        break;
    default:
        $order_by = 'p.created_at DESC';
        break;
}

// Fetch products from this category and its subcategories
$category_ids = [$category_id];
foreach ($subcategories as $sub) {
    $category_ids[] = $sub['id'];
}

$category_ids_str = implode(',', $category_ids);

// Count total products for pagination
$count_query = "SELECT COUNT(*) as total FROM products p WHERE p.category_id IN ($category_ids_str)";
$count_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $limit);

// Fetch products
$products_query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 JOIN categories c ON p.category_id = c.id 
                 WHERE p.category_id IN ($category_ids_str) 
                 ORDER BY $order_by 
                 LIMIT $offset, $limit";
$products_result = mysqli_query($conn, $products_query);
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/pages/products.php">Products</a></li>
            <?php if ($parent_category): ?>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/pages/category.php?id=<?php echo $parent_category['id']; ?>"><?php echo $parent_category['name']; ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $category['name']; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Category Header -->
        <div class="col-12 mb-4">
            <div class="category-header">
                <h1><?php echo $category['name']; ?></h1>
                <?php if (!empty($category['description'])): ?>
                      <p class="lead"><?php echo $category['description']; ?></p>
                  <?php endif; ?>
                  <?php 
                  $category_image = !empty($category['image']) ? IMAGES_URL . '/categories/' . $category['image'] : 'https://via.placeholder.com/150x150?text=' . $category['name'];
                  ?>
                  <img src="<?php echo $category_image; ?>" alt="<?php echo $category['name']; ?>" class="img-fluid">
              </div>
          </div>
          <!-- Subcategories (if any) -->
        <?php if (!empty($subcategories)): ?>
        <div class="col-12 mb-4">
            <div class="subcategories-section">
                <h3>Browse <?php echo $category['name']; ?> Categories</h3>
                <div class="row">
                    <?php foreach ($subcategories as $sub): ?>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="<?php echo SITE_URL; ?>/pages/category.php?id=<?php echo $sub['id']; ?>" class="subcategory-card">
                                <?php 
                                $sub_image = !empty($sub['image']) ? IMAGES_URL . '/categories/' . $sub['image'] : 'https://via.placeholder.com/150x150?text=' . $sub['name'];
                                ?>
                                <div class="card">
                                    <img src="<?php echo $sub_image; ?>" class="card-img-top" alt="<?php echo $sub['name']; ?>">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?php echo $sub['name']; ?></h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Products Section -->
        <div class="col-12">
            <!-- Sorting and Filters -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-muted"><?php echo $total_products; ?> products found</span>
                </div>
                <div class="d-flex">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Sort by: <?php echo ucfirst(str_replace('_', ' ', $sort)); ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                            <li><a class="dropdown-item <?php echo $sort == 'newest' ? 'active' : ''; ?>" href="?id=<?php echo $category_id; ?>&sort=newest">Newest</a></li>
                            <li><a class="dropdown-item <?php echo $sort == 'price_low' ? 'active' : ''; ?>" href="?id=<?php echo $category_id; ?>&sort=price_low">Price: Low to High</a></li>
                            <li><a class="dropdown-item <?php echo $sort == 'price_high' ? 'active' : ''; ?>" href="?id=<?php echo $category_id; ?>&sort=price_high">Price: High to Low</a></li>
                            <li><a class="dropdown-item <?php echo $sort == 'name_asc' ? 'active' : ''; ?>" href="?id=<?php echo $category_id; ?>&sort=name_asc">Name: A to Z</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <?php if (mysqli_num_rows($products_result) > 0): ?>
                <div class="row products-grid">
                    <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                        <?php 
                        $product_image = !empty($product['image']) ? IMAGES_URL . '/products/' . $product['image'] : 'https://via.placeholder.com/300x300?text=' . $product['name'];
                        $price_display = !empty($product['discount_price']) ? 
                            '<span class="price discount-price">' . formatPrice($product['discount_price']) . '</span>' . 
                            '<span class="original-price">' . formatPrice($product['price']) . '</span>' : 
                            '<span class="price">' . formatPrice($product['price']) . '</span>';
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card product-card">
                                <?php if ($product['category_id'] != $category_id): ?>
                                    <span class="category-badge"><?php echo $product['category_name']; ?></span>
                                <?php endif; ?>
                                <img src="<?php echo $product_image; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
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
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $category_id; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="alert alert-info">
                    <p class="mb-0">No products found in this category. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alert Container for AJAX messages -->
<div class="alert-container position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050;"></div>

<script>
$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').on('click', function() {
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/pages/add_to_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                // Show success message
                showAlert('Product added to cart successfully!', 'success');
                
                // Update cart count in navbar after a short delay
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            },
            error: function(xhr, status, error) {
                console.error('Error adding to cart:', error);
                showAlert('Failed to add product to cart. Please try again.', 'danger');
            }
        });
    });
    
    // Function to show alert messages
    function showAlert(message, type) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                        message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>';
        
        $('.alert-container').html(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>