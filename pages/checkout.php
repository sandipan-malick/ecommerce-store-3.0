<?php
require_once '../config/config.php';
require_once '../config/functions.php';

$page_title = "Checkout";
require_once '../includes/header.php';

$cart_items = [];
$total_price = 0;

if (!isLoggedIn()) {
    echo '<div class="alert alert-warning">You must <a href="login.php">log in</a> to proceed to checkout.</div>';
    require_once '../includes/footer.php';
    exit;
}

$user_id = getUserId();
$query = "SELECT c.id AS cart_id, c.quantity, p.* FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['quantity'] * $row['price'];
    $total_price += $row['subtotal'];
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    echo '<div class="alert alert-info">Your cart is empty. <a href="../index.php">Continue shopping</a>.</div>';
    require_once '../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Insert into orders table
    $insert_order = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_order);

    $shipping_address = "User's default address"; // You can replace this with a form input later
    $payment_method = "Cash on Delivery";         // Replace or make dynamic later

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "idss", $user_id, $total_price, $shipping_address, $payment_method);
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn); // Get the inserted order's ID

            // 2. Insert each item into order_items
            $item_stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
                mysqli_stmt_execute($item_stmt);
            }

            // 3. Clear the cart
            mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

            echo '<div class="alert alert-success">Your order has been placed successfully!</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to execute order. Error: ' . mysqli_stmt_error($stmt) . '</div>';
        }
        mysqli_stmt_close($stmt);
    } else {
        echo '<div class="alert alert-danger">Failed to prepare order statement: ' . mysqli_error($conn) . '</div>';
    }

    require_once '../includes/footer.php';
    exit;
}
?>

<!-- Display cart summary for confirmation -->
<div class="container py-5">
    <h2>Checkout</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo formatPrice($item['price']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo formatPrice($item['subtotal']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th><?php echo formatPrice($total_price); ?></th>
            </tr>
        </tfoot>
    </table>

    <form method="POST">
        <button type="submit" class="btn btn-success">Place Order</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
