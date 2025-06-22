<?php 
 $page_title = "Home"; 
 require_once 'includes/header.php'; 
 ?> 
  
 <div class="container"> 
     <!-- Hero Banner Slider --> 
     <div id="heroCarousel" class="carousel slide hero-slider" data-bs-ride="carousel"> 
         <div class="carousel-indicators"> 
             <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button> 
             <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button> 
             <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button> 
         </div> 
         <div class="carousel-inner"> 
             <div class="carousel-item active" style="background-image: url('https://via.placeholder.com/1200x400/3498db/ffffff?text=Fashion+Sale');"> 
                 <div class="carousel-caption d-none d-md-block"> 
                     <h2>Summer Fashion Sale</h2> 
                     <p>Up to 50% off on all summer clothing</p> 
                     <a href="pages/category.php?id=1" class="btn btn-light">Shop Now</a> 
                 </div> 
             </div> 
             <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1200x400/2ecc71/ffffff?text=Food+Deals');"> 
                 <div class="carousel-caption d-none d-md-block"> 
                     <h2>Grocery Deals</h2> 
                     <p>Fresh groceries at discounted prices</p> 
                     <a href="pages/category.php?id=2" class="btn btn-light">Shop Now</a> 
                 </div> 
             </div> 
             <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1200x400/e74c3c/ffffff?text=Electronics');"> 
                 <div class="carousel-caption d-none d-md-block"> 
                     <h2>Latest Electronics</h2> 
                     <p>Explore the newest gadgets and accessories</p> 
                     <a href="pages/category.php?id=3" class="btn btn-light">Shop Now</a> 
                 </div> 
             </div> 
         </div> 
         <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"> 
             <span class="carousel-control-prev-icon" aria-hidden="true"></span> 
             <span class="visually-hidden">Previous</span> 
         </button> 
         <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"> 
             <span class="carousel-control-next-icon" aria-hidden="true"></span> 
             <span class="visually-hidden">Next</span> 
         </button> 
     </div> 
  
     <!-- Featured Categories --> 
     <section class="category-section"> 
         <h2 class="text-center mb-4">Featured Categories</h2> 
         <div class="row"> 
             <?php 
             // Fetch main categories 
             $query = "SELECT * FROM categories WHERE parent_id IS NULL LIMIT 3"; 
             $result = mysqli_query($conn, $query); 
              
             if (mysqli_num_rows($result) > 0) { 
                 while ($category = mysqli_fetch_assoc($result)) { 
                     $image_url = !empty($category['image']) ? IMAGES_URL . '/categories/' . $category['image'] : 'https://via.placeholder.com/300x200?text=' . $category['name']; 
             ?> 
             <div class="col-md-4"> 
                 <div class="card category-card"> 
                     <img src="<?php echo $image_url; ?>" class="card-img-top" alt="<?php echo $category['name']; ?>"> 
                     <div class="card-body"> 
                         <h5 class="card-title"><?php echo $category['name']; ?></h5> 
                         <p class="card-text"><?php echo substr($category['description'], 0, 100) . '...'; ?></p> 
                         <a href="pages/category.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">Explore</a> 
                     </div> 
                 </div> 
             </div> 
             <?php 
                 } 
             } else { 
             ?> 
             <div class="col-md-4"> 
                 <div class="card category-card"> 
                     <img src="https://via.placeholder.com/300x200/3498db/ffffff?text=Dress" class="card-img-top" alt="Dress"> 
                     <div class="card-body"> 
                         <h5 class="card-title">Dress</h5> 
                         <p class="card-text">Explore our wide range of clothing items for men, women, and kids.</p> 
                         <a href="pages/category.php?id=1" class="btn btn-primary">Explore</a> 
                     </div> 
                 </div> 
             </div> 
             <div class="col-md-4"> 
                 <div class="card category-card"> 
                     <img src="https://via.placeholder.com/300x200/2ecc71/ffffff?text=Food" class="card-img-top" alt="Food"> 
                     <div class="card-body"> 
                         <h5 class="card-title">Food</h5> 
                         <p class="card-text">Discover fresh groceries, snacks, and beverages at great prices.</p> 
                         <a href="pages/category.php?id=2" class="btn btn-primary">Explore</a> 
                     </div> 
                 </div> 
             </div> 
             <div class="col-md-4"> 
                 <div class="card category-card"> 
                     <img src="https://via.placeholder.com/300x200/e74c3c/ffffff?text=Electronics" class="card-img-top" alt="Electronics"> 
                     <div class="card-body"> 
                         <h5 class="card-title">Electronics</h5> 
                         <p class="card-text">Check out the latest gadgets, accessories, and electronic devices.</p> 
                         <a href="pages/category.php?id=3" class="btn btn-primary">Explore</a> 
                     </div> 
                 </div> 
             </div> 
             <?php } ?> 
         </div> 
     </section> 
  
     <!-- Featured Products --> 
     <section class="featured-products"> 
         <h2 class="text-center mb-4">Featured Products</h2> 
         <div class="row"> 
             <?php 
             // Fetch featured products (products with discount) 
             $query = "SELECT p.*, c.name as category_name FROM products p  
                       JOIN categories c ON p.category_id = c.id  
                       WHERE p.discount_price IS NOT NULL  
                       ORDER BY RAND() LIMIT 8";

if (mysqli_num_rows($result) > 0) {
    while ($product = mysqli_fetch_assoc($result)) {
        $product_image = !empty($product['image']) ? IMAGES_URL . '/products/' . $product['image'] : 'https://via.placeholder.com/300x300?text=' . $product['name'];
?>
<div class="col-md-3 mb-4">
    <div class="card product-card h-100">
        <img src="<?php echo $product_image; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo $product['name']; ?></h5>
            <p class="card-text"><?php echo substr($product['description'], 0, 60) . '...'; ?></p>
            <p class="card-text text-muted">
                <small><?php echo $product['category_name']; ?></small>
            </p>
            <div class="mt-auto">
                <p class="price">
                    <span class="text-danger fw-bold">₹<?php echo $product['discount_price']; ?></span>
                    <del class="text-muted ms-2">₹<?php echo $product['price']; ?></del>
                </p>
                <a href="pages/product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-success w-100">View Details</a>
            </div>
        </div>
    </div>
</div>
<?php
    }
} else {
    echo '<div class="col-12"><p class="text-center">No featured products available at the moment.</p></div>';
}
?>
