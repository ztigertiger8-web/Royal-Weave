<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_id']))
{
    header("Location: Login.php");
    exit();
}

// Remove = hide the product from customers only.
// It does NOT delete the product from the database.
// Full database deletion is handled by delete_entirely_product.php.
if(!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    header("Location: Admin.php#inventory");
    exit();
}

$product_id = (int) $_GET['id'];

try
{
    $conn->beginTransaction();

    // Make the product inactive so it no longer appears to customers.
    $stmt = $conn->prepare("
        UPDATE product
        SET is_active = 0
        WHERE product_id = ?
    ");
    $stmt->execute([$product_id]);

    // Remove this product from current customer carts only.
    // This prevents customers from buying a product that admin removed from the store.
    $stmt = $conn->prepare("
        DELETE oi
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        JOIN product_variant pv ON oi.variant_id = pv.variant_id
        WHERE pv.product_id = ?
        AND o.is_cart = 1
    ");
    $stmt->execute([$product_id]);

    // Recalculate cart totals after removing those items.
    $stmt = $conn->prepare("
        UPDATE orders o
        SET total_amount = (
            SELECT COALESCE(SUM(oi.qty * oi.unit_price), 0)
            FROM order_items oi
            WHERE oi.order_id = o.order_id
        )
        WHERE o.is_cart = 1
    ");
    $stmt->execute();

    $conn->commit();
}
catch(PDOException $e)
{
    if($conn->inTransaction())
    {
        $conn->rollBack();
    }

    die("Could not remove product from customer side: " . $e->getMessage());
}

header("Location: Admin.php#inventory");
exit();
?>
