<?php
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(SITE_URL);
}

$order_id = (int)$_GET['id'];
$user_id = getUserId();

// Fetch order details
$order_query = "SELECT o.*, u.full_name, u.email, u.phone 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               WHERE o.id = $order_id AND o.user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    // Order not found or doesn't belong to this user
    redirect(SITE_URL);
}

$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$items_query = "SELECT oi.*, p.name, p.image, c.name as category_name 
               FROM order_items oi 
               JOIN products p ON oi.product_id = p.id 
               JOIN categories c ON p.category_id = c.id 
               WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

$page_title = "Order Confirmation";
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success fa-5x"></i>
                    </div>
                    <h1 class="mb-3">Thank You for Your Order!</h1>
                    <p class="lead mb-4">Your order has been placed successfully and is being processed.</p>
                    <div class="order-info mb-4">
                        <h5>Order #<?php echo $order_id; ?></h5>
                        <p class="text-muted"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?php echo SITE_URL; ?>/pages/order_details.php?id=<?php echo $order_id; ?>" class="btn btn-primary">
                            <i class="fas fa-info-circle me-2"></i>View Order Details
                        </a>
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-home me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Shipping Address</h6>
                            <p class="mb-0"><?php echo $order['shipping_address']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Method</h6>
                            <p class="mb-0">
                                <?php 
                                switch ($order['payment_method']) {
                                    case 'cod':
                                        echo 'Cash on Delivery (COD)';
                                        break;
                                    case 'card':
                                        echo 'Credit/Debit Card';
                                        break;
                                    case 'upi':
                                        echo 'UPI';
                                        break;
                                    default:
                                        echo ucfirst($order['payment_method']);
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <h6>Order Items</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                    <?php 
                                    $image_url = !empty($item['image']) ? IMAGES_URL . '/products/' . $item['image'] : 'https://via.placeholder.com/50x50?text=' . $item['name'];
                                    $item_total = $item['price'] * $item['quantity'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $image_url; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $item['name']; ?></h6>
                                                    <small class="text-muted"><?php echo $item['category_name']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo formatPrice($item['price']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo formatPrice($item_total); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                    <td class="text-end"><?php echo formatPrice($order['total_amount']); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Shipping</strong></td>
                                    <td class="text-end">Free</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                                    <td class="text-end"><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- What's Next Section -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">What's Next?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="p-3">
                                <i class="fas fa-box fa-2x mb-3 text-primary"></i>
                                <h6>Order Processing</h6>
                                <p class="small text-muted mb-0">We're preparing your order for shipment.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="p-3">
                                <i class="fas fa-shipping-fast fa-2x mb-3 text-primary"></i>
                                <h6>Shipping</h6>
                                <p class="small text-muted mb-0">Your order will be shipped within 1-2 business days.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3">
                                <i class="fas fa-envelope fa-2x mb-3 text-primary"></i>
                                <h6>Updates</h6>
                                <p class="small text-muted mb-0">You'll receive email updates about your order status.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>