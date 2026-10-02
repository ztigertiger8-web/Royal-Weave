<?php
session_start();
include("db.php");

if(!isset($_SESSION['customer_id']))
{
    echo "<script>alert('Please login first'); window.location.href='Login.php';</script>";
    exit();
}

if(isset($_POST['add_to_cart']))
{
    $customer_id = $_SESSION['customer_id'];
    $variant_id = $_POST['variant_id'];
    $qty = $_POST['qty'];

    if($qty < 1)
    {
        echo "<script>alert('Invalid quantity'); window.location.href='MainFrame.php';</script>";
        exit();
    }

    // Get product price and stock
    $stmt = $conn->prepare("SELECT price, stock_qty FROM product_variant WHERE variant_id = ?");
    $stmt->execute([$variant_id]);
    $variant = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$variant)
    {
        echo "<script>alert('Product not found'); window.location.href='MainFrame.php';</script>";
        exit();
    }

    if($qty > $variant['stock_qty'])
    {
        echo "<script>alert('Not enough stock'); window.location.href='MainFrame.php';</script>";
        exit();
    }

    // Check if customer has active cart
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE customer_id = ? AND is_cart = 1 LIMIT 1");
    $stmt->execute([$customer_id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if($cart)
    {
        $order_id = $cart['order_id'];
    }
    else
    {
        $stmt = $conn->prepare("INSERT INTO orders(customer_id, is_cart, status, payment_method, payment_status, total_amount) VALUES(?, 1, 'pending', 'cash', 'pending', 0)");
        $stmt->execute([$customer_id]);
        $order_id = $conn->lastInsertId();
    }

    // Check if item already in cart
    $stmt = $conn->prepare("SELECT order_item_id, qty FROM order_items WHERE order_id = ? AND variant_id = ?");
    $stmt->execute([$order_id, $variant_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if($item)
    {
        $new_qty = $item['qty'] + $qty;

        if($new_qty > $variant['stock_qty'])
        {
            echo "<script>alert('Quantity exceeds available stock'); window.location.href='MainFrame.php';</script>";
            exit();
        }

        $stmt = $conn->prepare("UPDATE order_items SET qty = ? WHERE order_item_id = ?");
        $stmt->execute([$new_qty, $item['order_item_id']]);
    }
    else
    {
        $stmt = $conn->prepare("INSERT INTO order_items(order_id, variant_id, qty, unit_price) VALUES(?,?,?,?)");
        $stmt->execute([$order_id, $variant_id, $qty, $variant['price']]);
    }

    // Update total amount
    $stmt = $conn->prepare("
        UPDATE orders
        SET total_amount = (
            SELECT SUM(qty * unit_price)
            FROM order_items
            WHERE order_id = ?
        )
        WHERE order_id = ?
    ");
    $stmt->execute([$order_id, $order_id]);

    echo "<script>alert('Product added to cart'); window.location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
    exit();
}
?>