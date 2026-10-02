<?php
session_start();
include("db.php");

if(!isset($_SESSION['customer_id']))
{
    header("Location: Login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Get current cart
$stmt = $conn->prepare("
    SELECT * 
    FROM orders
    WHERE customer_id = ?
    AND is_cart = 1
    LIMIT 1
");

$stmt->execute([$customer_id]);

$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$cart)
{
    echo "<script>alert('Cart is empty'); window.location.href='Cart.php';</script>";
    exit();
}

$order_id = $cart['order_id'];


// Get cart items
$stmt = $conn->prepare("
    SELECT 
        oi.variant_id,
        oi.qty,
        v.stock_qty

    FROM order_items oi

    JOIN product_variant v
        ON oi.variant_id = v.variant_id

    WHERE oi.order_id = ?
");

$stmt->execute([$order_id]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Check stock
foreach($items as $item)
{
    if($item['qty'] > $item['stock_qty'])
    {
        echo "<script>alert('Not enough stock'); window.location.href='Cart.php';</script>";
        exit();
    }
}


// Reduce stock
foreach($items as $item)
{
    $stmt = $conn->prepare("
        UPDATE product_variant
        SET stock_qty = stock_qty - ?
        WHERE variant_id = ?
    ");

    $stmt->execute([$item['qty'], $item['variant_id']]);
}


// Convert cart to completed order
$stmt = $conn->prepare("
    UPDATE orders
    SET 
        is_cart = 0,
        status = 'paid',
        payment_status = 'paid'
    WHERE order_id = ?
");

$stmt->execute([$order_id]);

header("Location: Success.html");
exit();
?>