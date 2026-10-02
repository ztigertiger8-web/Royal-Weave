<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_id']))
{
    header("Location: Login.php");
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id']))
{
    die("Invalid product ID");
}

$product_id = (int) $_GET['id'];

try
{
    $conn->beginTransaction();

    // Get all variants for this product first.
    $stmt = $conn->prepare("SELECT variant_id FROM product_variant WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $variant_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete related cart/order items first because order_items blocks variant deletion.
    if(count($variant_ids) > 0)
    {
        $placeholders = implode(',', array_fill(0, count($variant_ids), '?'));

        $stmt = $conn->prepare("DELETE FROM order_items WHERE variant_id IN ($placeholders)");
        $stmt->execute($variant_ids);
    }

    // Delete variants. Product variants also have ON DELETE CASCADE, but this makes the delete clear.
    $stmt = $conn->prepare("DELETE FROM product_variant WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Delete the product itself.
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->execute([$product_id]);

    $conn->commit();

    header("Location: Admin.php#inventory");
    exit();
}
catch(PDOException $e)
{
    if($conn->inTransaction())
    {
        $conn->rollBack();
    }

    die("Could not delete product entirely: " . $e->getMessage());
}
?>
