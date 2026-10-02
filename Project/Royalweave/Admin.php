<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin_id']))
{
    header("Location: Login.php");
    exit();
}


// Add Product
if(isset($_POST['publish']))
{
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $origin = $_POST['origin'];
    $texture = $_POST['texture'];
    $kind = $_POST['kind'];

    if(empty($name))
    {
        die("Product name is required");
    }

    if(empty($description))
    {
        die("Description is required");
    }

    if($price <= 0)
    {
        die("Invalid price");
    }

    if($stock < 0)
    {
        die("Invalid stock quantity");
    }

    $image = $_FILES['image']['name'];

    if($kind == "fabric")
    {
        $target = "images/Fabric/" . $image;
    }
    else
    {
        $target = "images/Shemagh/" . $image;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];

    if(!in_array($_FILES['image']['type'], $allowed_types))
    {
        die("Invalid image type");
    }

    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $category_id = ($kind == "fabric") ? 1 : 2;

    $stmt = $conn->prepare("
        SELECT category_id
        FROM category
        WHERE category_id = ?
    ");

    $stmt->execute([$category_id]);

    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$category)
    {
        die("Category does not exist");
    }

    $stmt = $conn->prepare("
        INSERT INTO product
        (
            category_id,
            name,
            description,
            product_kind,
            main_image_filename,
            created_by_admin_id
        )
        VALUES(?,?,?,?,?,?)
    ");

    $stmt->execute([
        $category_id,
        $name,
        $description,
        $kind,
        $image,
        $_SESSION['admin_id']
    ]);

    $product_id = $conn->lastInsertId();

    $stmt = $conn->prepare("
        INSERT INTO product_variant
        (
            product_id,
            sku,
            price,
            stock_qty,
            origin_country,
            weave_type,
            image_filename
        )
        VALUES(?,?,?,?,?,?,?)
    ");

    $sku = "SKU-" . rand(1000,9999);

    $stmt->execute([
        $product_id,
        $sku,
        $price,
        $stock,
        $origin,
        $texture,
        $image
    ]);

    echo "<script>alert('Product Added Successfully');</script>";
}


$admin_search = "";

if(isset($_GET['admin_search']))
{
    $admin_search = trim($_GET['admin_search']);
}

if($admin_search != "")
{
    $stmt = $conn->prepare("
        SELECT
            p.product_id,
            p.name,
            p.product_kind,
            p.is_active,

            v.variant_id,
            v.price,
            v.stock_qty,
            v.origin_country,
            v.weave_type,
            v.image_filename

        FROM product p

        JOIN product_variant v
            ON p.product_id = v.product_id

        WHERE p.name LIKE ?
        OR p.product_id LIKE ?

        ORDER BY p.product_id DESC
    ");

    $stmt->execute([
        "%" . $admin_search . "%",
        "%" . $admin_search . "%"
    ]);
}
else
{
    $stmt = $conn->prepare("
        SELECT
            p.product_id,
            p.name,
            p.product_kind,
            p.is_active,

            v.variant_id,
            v.price,
            v.stock_qty,
            v.origin_country,
            v.weave_type,
            v.image_filename

        FROM product p

        JOIN product_variant v
            ON p.product_id = v.product_id

        ORDER BY p.product_id DESC
    ");

    $stmt->execute();
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="admin.js" defer></script>
</head>

<body class="body-page" onload="adminStart()">

    <div class="header-area">
        <div>
            <h2 class="main-title">Royal Weave Hub</h2>
            <p id="adminName" style="margin: 6px 0 0 0; color: #A8A9AD; font-size: 13px;"></p>
        </div>
        <a href="MainFrame.php" target="_blank" class="preview-button">
            Live Store Preview ↗
        </a>
    </div>

    <div class="content-box">
        <h3 class="gold-side-border">Add New Fabric</h3>

        <form id="addProductForm" method="POST" enctype="multipart/form-data" onsubmit="return validateAddProduct()">
            <table cellpadding="10" style="width: 100%;">
                <tr>
                    <td width="150"><b>Product Name:</b></td>
                    <td>
                        <input type="text" name="name" id="productName" placeholder="e.g. Luxury Japanese Silk" class="input-field">
                    </td>
                </tr>

                <tr>
                    <td><b>Description:</b></td>
                    <td>
                        <textarea name="description" id="productDesc" placeholder="Texture, Origin, and Length..." class="text-area-field"></textarea>
                    </td>
                </tr>

                <tr>
                    <td><b>Price (SAR):</b></td>
                    <td>
                        <input type="number" name="price" id="productPrice" placeholder="500" class="price-input">
                    </td>
                </tr>

                <tr>
                    <td><b>Stock Quantity:</b></td>
                    <td>
                        <input type="number" name="stock" id="productStock" placeholder="25" class="price-input">
                    </td>
                </tr>

                <tr>
                    <td><b>Product Type:</b></td>
                    <td>
                        <select name="kind" id="productCategory" class="input-field">
                            <option value="">Select product type</option>
                            <option value="fabric">Fabric</option>
                            <option value="shemagh">Shemagh</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><b>Origin Country:</b></td>
                    <td>
                        <input type="text" name="origin" id="productOrigin" placeholder="Japan" class="input-field">
                    </td>
                </tr>

                <tr>
                    <td><b>Texture / Material:</b></td>
                    <td>
                        <input type="text" name="texture" id="productTexture" placeholder="Soft / Silky" class="input-field">
                    </td>
                </tr>

                <tr>
                    <td><b>Upload Image:</b></td>
                    <td>
                        <input type="file" name="image" id="productImage" accept="image/*" class="upload-button">
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td>
                        <button type="submit" name="publish" class="add-button">Publish Product</button>
                        <p id="adminMsg" style="color: #1B263B; font-weight: bold; font-size: 13px;"></p>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <div class="content-box">
        <h3 class="grey-side-border">Manage Existing Inventory</h3>
        
        <div style="display:flex; gap:10px; align-items:center;" id="inventory">
            <form method="GET" action="Admin.php#inventory" style="margin:0;">
                <input 
                    type="search" 
                    name="admin_search" 
                    id="adminSearch"
                    placeholder="Search by name or ID..." 
                    class="search-field" 
                    value="<?php echo isset($_GET['admin_search']) ? htmlspecialchars($_GET['admin_search']) : ''; ?>"
                >
            </form>

            <a href="Admin.php#inventory" class="search-button" style="text-decoration:none; display:inline-block;">Show All</a>
        </div>

        <table class="inventory-table">
            <tr class="table-header-row">
                <th style="padding: 15px;">Current Image</th>
                <th>Product Details</th>
                <th>Price (SAR)</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>

            <tbody id="productTableBody">
            <?php foreach($products as $p): ?>
            <tr>
                <td style="padding: 15px;">
                    <img src="images/<?php echo $p['product_kind'] == 'fabric' ? 'Fabric' : 'Shemagh'; ?>/<?php echo $p['image_filename']; ?>" width="60" class="product-image">
                </td>

                <td style="padding: 10px; text-align: left;">
                    <b>ID: <?php echo $p['product_id']; ?></b><br>
                    <b>Name:</b> <?php echo $p['name']; ?><br>
                    <b>Origin:</b> <?php echo $p['origin_country']; ?><br>
                    <b>Product Type:</b> <?php echo $p['product_kind']; ?><br>
                    <b>Texture:</b> <?php echo $p['weave_type']; ?>
                </td>

                <td><?php echo $p['price']; ?></td>
                <td><?php echo $p['stock_qty']; ?></td>

                <td>
                    <a href="EditAdminProduct.php?id=<?php echo $p['product_id']; ?>" class="update-button" style="text-decoration:none; display:inline-block; margin-bottom:8px;">Update</a><br>

                    <a 
                        href="delete_entirely_product.php?id=<?php echo $p['product_id']; ?>" 
                        class="remove-entirely-button" 
                        style="text-decoration:none; display:inline-block;"
                        onclick="return confirmRemoveEntirelyProduct()"
                    >
                        RemoveEntirly
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="logout-footer">
        <a href="logout.php" style="color: #1B263B; text-decoration: none; font-weight: bold;">← Secure Logout</a>
    </div>

</body>
</html>