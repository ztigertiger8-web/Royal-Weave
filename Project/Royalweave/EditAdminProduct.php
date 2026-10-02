<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_id']))
{
    header("Location: Login.php");
    exit();
}

if(!isset($_GET['id']))
{
    header("Location: Admin.php");
    exit();
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT p.product_id, p.name, p.description, p.product_kind,
           v.variant_id, v.price, v.stock_qty, v.origin_country, v.weave_type, v.image_filename
    FROM product p
    JOIN product_variant v ON p.product_id = v.product_id
    WHERE p.product_id = ?
");

$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product)
{
    header("Location: Admin.php");
    exit();
}

if(isset($_POST['update']))
{
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $origin = $_POST['origin'];
    $kind = $_POST['kind'];
    $texture = $_POST['texture'];

    $stmt = $conn->prepare("
        UPDATE product
        SET 
            name = ?,
            description = ?,
            product_kind = ?
        WHERE product_id = ?
    ");

    $stmt->execute([
        $name,
        $description,
        $kind,
        $product_id
    ]);

    $stmt = $conn->prepare("
        UPDATE product_variant
        SET price = ?, stock_qty = ?, origin_country = ?, weave_type = ?
        WHERE variant_id = ?
    ");
    $stmt->execute([$price, $stock, $origin, $texture, $product['variant_id']]);
    if(!empty($_FILES['image']['name']))
{
    $image = $_FILES['image']['name'];

    if($kind == "fabric")
    {
        $target = "images/Fabric/" . $image;
    }
    else
    {
        $target = "images/Shemagh/" . $image;
    }

    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("
        UPDATE product
        SET main_image_filename = ?
        WHERE product_id = ?
    ");
    $stmt->execute([$image, $product_id]);

    $stmt = $conn->prepare("
        UPDATE product_variant
        SET image_filename = ?
        WHERE variant_id = ?
    ");
    $stmt->execute([$image, $product['variant_id']]);
}

    header("Location: Admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="body-page">

<div class="content-box">
    <h3 class="gold-side-border">Update Product</h3>

    <form method="POST" enctype="multipart/form-data">        <p>Product Name:<br><input type="text" name="name" value="<?php echo $product['name']; ?>" class="input-field"></p>
        <p>Description:<br><textarea name="description" class="text-area-field"><?php echo $product['description']; ?></textarea></p>
        <p>Price:<br><input type="number" name="price" value="<?php echo $product['price']; ?>" class="price-input"></p>
        <p>Stock:<br><input type="number" name="stock" value="<?php echo $product['stock_qty']; ?>" class="price-input"></p>
        <p>Origin:<br><input type="text" name="origin" value="<?php echo $product['origin_country']; ?>" class="input-field"></p>
        <p>Product Type:<br>
            <select name="kind" class="input-field">
                <option value="fabric"<?php if($product['product_kind'] == 'fabric') echo 'selected'; ?>>Fabric</option>
                <option value="shemagh"<?php if($product['product_kind'] == 'shemagh') echo 'selected'; ?>>Shemagh</option>
            </select>
        </p>
        <p>Texture / Material:<br><input type="text" name="texture" value="<?php echo $product['weave_type']; ?>" class="input-field"></p>
        <p>Update Image:<br><input type="file" name="image" accept="image/*" class="upload-button"></p>
        <button type="submit" name="update" class="update-button">Update</button>
        <a href="Admin.php" class="delete-button" style="text-decoration:none; padding:8px 15px;">Cancel</a>
    </form>
</div>

</body>
</html>