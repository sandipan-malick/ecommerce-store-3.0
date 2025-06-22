<?php
require_once '../config/config.php';
require_once '../config/functions.php';

// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle remove item
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);

    if (isLoggedIn()) {
        $user_id = getUserId();
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }

    header("Location: cart.php");
    exit();
}

$page_title = "Your Cart";
require_once '../includes/header.php';

$cart_items = [];
$total_price = 0;

if (isLoggedIn()) {
    $user_id = getUserId();
    $sql = "SELECT c.id AS cart_id, c.quantity, p.* FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total_price += $row['subtotal'];
        $cart_items[] = $row;
    }
} elseif (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $product_query = "SELECT * FROM products WHERE id = $product_id";
        $product_result = mysqli_query($conn, $product_query);
        if ($product_result && mysqli_num_rows($product_result) > 0) {
            $product = mysqli_fetch_assoc($product_result);
            $product['quantity'] = $item['quantity'];
            $product['subtotal'] = $product['price'] * $item['quantity'];
            $total_price += $product['subtotal'];
            $cart_items[] = $product;
        }
    }
}
?>

<!-- HTML part of the cart -->
<div class="container mt-4">
    <h2>Your Shopping Cart</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>₹<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                        <td>
                            <a href="cart.php?remove=<?= $item['id'] ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to remove this item?');">
                               Remove
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>₹<?= number_format($total_price, 2) ?></strong></td>
                </tr>
            <?php else: ?>
                <tr><td colspan="5">Your cart is empty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($cart_items)): ?>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
