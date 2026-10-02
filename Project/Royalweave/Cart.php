<?php
session_start();
include("db.php");

if(!isset($_SESSION['customer_id']))
{
    header("Location: Login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if(isset($_POST['update_qty']))
{
    $order_item_id = $_POST['order_item_id'];
    $qty = $_POST['qty'];

    $stmt = $conn->prepare("
        SELECT v.stock_qty
        FROM order_items oi
        JOIN product_variant v ON oi.variant_id = v.variant_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE oi.order_item_id = ?
        AND o.customer_id = ?
        AND o.is_cart = 1
    ");
    $stmt->execute([$order_item_id, $customer_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$item)
    {
        header("Location: Cart.php");
        exit();
    }

    if($qty < 1)
    {
        echo "<script>alert('Invalid quantity'); window.location.href='Cart.php';</script>";
        exit();
    }

    if($qty > $item['stock_qty'])
    {
        echo "<script>alert('Quantity exceeds available stock'); window.location.href='Cart.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("
        UPDATE order_items
        SET qty = ?
        WHERE order_item_id = ?
    ");
    $stmt->execute([$qty, $order_item_id]);

    $stmt = $conn->prepare("
        UPDATE orders
        SET total_amount = (
            SELECT SUM(qty * unit_price)
            FROM order_items
            WHERE order_id = orders.order_id
        )
        WHERE order_id = (
            SELECT order_id
            FROM order_items
            WHERE order_item_id = ?
        )
    ");
    $stmt->execute([$order_item_id]);

    header("Location: Cart.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT 
        oi.order_item_id,
        oi.qty,
        oi.unit_price,

        p.name,

        v.variant_id,
        v.stock_qty,
        v.image_filename

    FROM orders o

    JOIN order_items oi 
        ON o.order_id = oi.order_id

    JOIN product_variant v 
        ON oi.variant_id = v.variant_id

    JOIN product p 
        ON v.product_id = p.product_id

    WHERE o.customer_id = ?
    AND o.is_cart = 1
");

$stmt->execute([$customer_id]);

$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

foreach($cart_items as $item)
{
    $total += $item['qty'] * $item['unit_price'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="cart-page-body">

    <div class="header-center">
        <img src="images/logo2.png" alt="Logo" width="120">
        <h1 class="shopping-cart-title">Shopping Cart</h1>
    </div>

    <div class="cart-main-box">
        <table class="cart-data-table">
            <tr class="cart-header-row">
                <th style="padding: 10px;">Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach($cart_items as $item): ?>
            <tr class="item-row">
              <td style="padding: 15px 0;">
                    <img src="images/Fabric/<?php echo $item['image_filename']; ?>" width="50" class="product-image">
                    <?php echo $item['name']; ?>
                </td>
                <td><?php echo $item['unit_price']; ?> SAR</td>
                <td>
                    <form method="POST" id="form_<?php echo $item['order_item_id']; ?>">
                        <input type="hidden" name="update_qty" value="1">
                        <input type="hidden" name="order_item_id" value="<?php echo $item['order_item_id']; ?>">
                        <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" max="<?php echo $item['stock_qty']; ?>" class="quantity-input" onchange="document.getElementById('form_<?php echo $item['order_item_id']; ?>').submit();">
                    </form>
                </td>
                <td><?php echo $item['qty'] * $item['unit_price']; ?> SAR</td>
                <td>
                    <a href="delete_cart_item.php?id=<?php echo $item['order_item_id']; ?>" class="delete-item-button" style="text-decoration: none;">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="cart-bottom-area">
            <a href="clear_cart.php" class="delete-all-button" style="text-decoration: none; display: inline-block;">Delete All Items</a>            <h3 class="total-price-text" id="subtotal">Total: <?php echo $total; ?> SAR</h3>
            <br><br>
            <a href="complete_purchase.php" class="checkout-button" style="text-decoration: none; display: inline-block;">Complete Purchase (Buy)</a>
        </div>
    </div>

    <div class="back-to-store">
        <a href="MainFrame.php" class="store-link">Continue Shopping</a>
    </div>

</body>
</html>