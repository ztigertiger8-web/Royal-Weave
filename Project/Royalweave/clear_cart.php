<?php
session_start();
include("db.php");

if(!isset($_SESSION['customer_id']))
{
    header("Location: Login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$stmt = $conn->prepare("
    DELETE oi
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.customer_id = ?
    AND o.is_cart = 1
");

$stmt->execute([$customer_id]);

$stmt = $conn->prepare("
    UPDATE orders
    SET total_amount = 0
    WHERE customer_id = ?
    AND is_cart = 1
");

$stmt->execute([$customer_id]);

header("Location: Cart.php");
exit();
?>