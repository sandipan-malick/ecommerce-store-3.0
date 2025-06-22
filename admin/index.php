<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

$page_title = "Admin Dashboard";
require_once 'includes/header.php';

// Fetch order details with user info
$sql = "SELECT orders.id AS order_id, orders.total_amount, orders.created_at, users.full_name, users.email 
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        ORDER BY orders.created_at DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <h2>Admin Panel - Order Details</h2>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Total Amount</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No orders found.</div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
