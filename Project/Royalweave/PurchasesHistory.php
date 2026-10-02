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
    SELECT 
        o.order_date,
        oi.qty,
        oi.unit_price,
        p.name,
        v.image_filename
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product_variant v ON oi.variant_id = v.variant_id
    JOIN product p ON v.product_id = p.product_id
    WHERE o.customer_id = ?
    AND o.is_cart = 0
    AND o.status = 'paid'
    ORDER BY o.order_date DESC
");

$stmt->execute([$customer_id]);
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Previous Purchases</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body class="history-page-body">

    <div class="header-center" style="text-align:center; margin-bottom:30px;">
        <img src="images/logo2.png" width="120">
        <h1 style="color:#1B263B;">Previous Purchases</h1>
    </div>

    <div class="history-main-container">
        <table class="history-data-table">
            <tr class="history-table-header">
                <th>Product</th>
                <th>Date</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
            <?php foreach($purchases as $purchase): ?>
            <tr class="history-row-border">
                <td style="padding:15px;"><img src="images/Fabric/<?php echo $purchase['image_filename']; ?>" width="60" style="border-radius:10px;"><br><?php echo $purchase['name']; ?></td>
                <td><?php echo $purchase['order_date']; ?></td>
                <td><?php echo $purchase['qty']; ?></td>
                <td><?php echo $purchase['unit_price'] * $purchase['qty']; ?> SAR</td>
            </tr>
            <?php endforeach; ?>
            </table>

        <div class="button-center-area" style="text-align:center; margin-top:30px;">
            <a href="MainFrame.php" style="color:#1B263B; font-weight:bold; text-decoration:none;">← Back to Store</a>
        </div>
    </div>

</body>
</html>