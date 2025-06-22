<?php
require_once '../config/config.php';
require_once '../config/functions.php';

$page_title = "Your Orders";
require_once '../includes/header.php';

if (!isLoggedIn()) {
    echo "<div class='alert alert-warning'>Please <a href='login.php'>log in</a> to view your orders.</div>";
    require_once '../includes/footer.php';
    exit;
}

$user_id = getUserId();
$query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo "<div class='alert alert-danger'>Error fetching orders: " . mysqli_error($conn) . "</div>";
    require_once '../includes/footer.php';
    exit;
}
?>

<div class="container py-5">
    <h2>Your Orders</h2>
    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class="alert alert-info">You have not placed any orders yet.</div>
    <?php else: ?>
        <?php while ($order = mysqli_fetch_assoc($result)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Order #<?php echo htmlspecialchars($order['id']); ?></strong> —
                    Total: <?php echo formatPrice($order['total_amount']); ?> —
                    Date: <?php echo htmlspecialchars($order['created_at']); ?>
                </div>
                <div class="card-body">
                    <?php
                    $order_id = $order['id'];
                    $items_query = "
                        SELECT oi.quantity, oi.price, p.name 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = $order_id
                    ";
                    $items_result = mysqli_query($conn, $items_query);
                    ?>
                    <?php if (mysqli_num_rows($items_result) > 0): ?>
                        <ul class="list-group">
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($item['quantity']) . 'x ' . htmlspecialchars($item['name']); ?>
                                    <span><?php echo formatPrice($item['price']); ?></span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No items found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
