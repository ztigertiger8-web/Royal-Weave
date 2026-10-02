<?php
session_start();
include("db.php");

if(!isset($_SESSION['customer_id']))
{
    header("Location: Login.php");
    exit();
}

if(isset($_GET['id']))
{
    $order_item_id = $_GET['id'];
    $customer_id = $_SESSION['customer_id'];

    $stmt = $conn->prepare("
        DELETE oi
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        WHERE oi.order_item_id = ?
        AND o.customer_id = ?
        AND o.is_cart = 1
    ");
    $stmt->execute([$order_item_id, $customer_id]);
}

header("Location: Cart.php");
exit();
?>