<?php
require_once '../includes/header.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/pages/login.php');
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect(SITE_URL . '/pages/orders.php');
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
    redirect(SITE_URL . '/pages/orders.php');
}

$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$items_query = "SELECT oi.*, p.name, p.image, c.name as category_name 
               FROM order_items oi 
               JOIN products p ON oi.product_id = p.id 
               JOIN categories c ON p.category_id = c.id 
               WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

$page_title = "Order #$order_id Details";

// Get order status class
$status_class = '';
switch ($order['order_status']) {
    case 'pending':
        $status_class = 'bg-warning';
        break;
    case 'processing':
        $status_class = 'bg-info';
        break;
    case 'shipped':
        $status_class = 'bg-primary';
        break;
    case 'delivered':
        $status_class = 'bg-success';
        break;
    case 'cancelled':
        $status_class = 'bg-danger';
        break;
    default:
        $status_class = 'bg-secondary';
}
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/pages/orders.php">My Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Order #<?php echo $order_id; ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h4 mb-0">Order #<?php echo $order_id; ?></h1>
                            <p class="text-muted mb-0"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div>
                            <span class="badge <?php echo $status_class; ?> p-2">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                mysqli_data_seek($items_result, 0); // Reset result pointer
                                while ($item = mysqli_fetch_assoc($items_result)): 
                                ?>
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
                            <tfoot class="table-light">
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
            
            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Placed</h6>
                                <p class="text-muted mb-0"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></p>
                            </div>
                        </li>
                        
                        <?php if (in_array($order['order_status'], ['processing', 'shipped', 'delivered'])): ?>
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Processing</h6>
                                <p class="text-muted mb-0">Your order is being prepared for shipment</p>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (in_array($order['order_status'], ['shipped', 'delivered'])): ?>
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Shipped</h6>
                                <p class="text-muted mb-0">Your order has been shipped</p>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($order['order_status'] === 'delivered'): ?>
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Delivered</h6>
                                <p class="text-muted mb-0">Your order has been delivered</p>
                            </div>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($order['order_status'] === 'cancelled'): ?>
                        <li class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Cancelled</h6>
                                <p class="text-muted mb-0">Your order has been cancelled</p>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Shipping Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Shipping Information</h5>
                </div>
                <div class="card-body">
                    <h6>Shipping Address</h6>
                    <p><?php echo $order['shipping_address']; ?></p>
                    
                    <h6>Contact Information</h6>
                    <p class="mb-1"><strong>Name:</strong> <?php echo $order['full_name']; ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?php echo $order['email']; ?></p>
                    <p class="mb-0"><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Payment Method:</strong>
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
                    <p class="mb-0"><strong>Payment Status:</strong> 
                        <?php if ($order['payment_method'] === 'cod'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php else: ?>
                            <span class="badge bg-success">Paid</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <!-- Need Help -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions or concerns about your order, please contact our customer support.</p>
                    <div class="d-grid">
                        <a href="#" class="btn btn-outline-primary">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 30px;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-bottom: 25px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background-color: #4CAF50;
    left: -30px;
    top: 5px;
}

.timeline:before {
    content: '';
    position: absolute;
    left: -23px;
    top: 0;
    height: 100%;
    width: 2px;
    background-color: #e0e0e0;
}
</style>

<?php require_once '../includes/footer.php'; ?>