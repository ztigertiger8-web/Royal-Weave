<?php
session_start();
include("db.php");

$search = "";

if(isset($_GET['search']))
{
    $search = trim($_GET['search']);
}

if($search != "")
{
    $stmt = $conn->prepare("
        SELECT 
            p.product_id,
            p.name,
            p.description,
            p.product_kind,
            p.main_image_filename,
            v.variant_id,
            v.price,
            v.stock_qty,
            v.origin_country,
            v.weave_type,
            v.image_filename
        FROM product p
        JOIN product_variant v ON p.product_id = v.product_id
        WHERE p.is_active = 1
        AND p.name LIKE ?
        ORDER BY p.product_kind, p.product_id
    ");

    $stmt->execute(["%" . $search . "%"]);
}
else
{
    $stmt = $conn->prepare("
        SELECT 
            p.product_id,
            p.name,
            p.description,
            p.product_kind,
            p.main_image_filename,
            v.variant_id,
            v.price,
            v.stock_qty,
            v.origin_country,
            v.weave_type,
            v.image_filename
        FROM product p
        JOIN product_variant v ON p.product_id = v.product_id
        WHERE p.is_active = 1
        ORDER BY p.product_kind, p.product_id
    ");

    $stmt->execute();
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <title>Main page</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    
</head>
<body class="main-page-body">
    
<header class="main-header">
    <div style="display: flex; align-items: center; gap: 20px;">
        <img src="images/logo2.png" alt="Logo" width="120" class="main-logo">
        <div style="color: white;">
            <p style="margin: 0; font-weight: bold; color: #B59A5D;">Searching:</p>
            <form method="GET" action="MainFrame.php">
                <input id="searchInput" name="search" type="search" placeholder="search here..." class="search-input-field" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>
        </div>
    </div>
    <nav class="navigation-links">
        <a href="#fabric" class="nav-item-button">Fabric</a> 
        <a href="#shmaq" class="nav-item-button">Shmaq</a>
        <?php if(isset($_SESSION['customer_id'])): ?>
            <a href="EditProfile.php" class="gray-button-link">👤 Profile</a>
            <a href="PurchasesHistory.php" class="gray-button-link">🧾 PurchasesHistory</a>
            <a href="logout.php" class="logout-button-danger">Logout</a>
        <?php else: ?>
            <a href="Login.php" class="gray-button-link">Login</a>
        <?php endif; ?>
    </nav>
        <?php if(isset($_SESSION['customer_id'])): ?>
            <a href="Cart.php">
        <?php else: ?>
            <a href="Login.php" onclick="alert('Please login first to view your cart!')">
        <?php endif; ?>    <img src="images/Cart_image.png" alt="Cart" width="100" class="cart-icon-style">
        </a>
</header>

    <div class="section-title-box">
        <h2 class="title-underline">Fabric Collection</h2>
    </div>
    
    <div class="main-container" id="fabric" style="text-align: center; margin-top: 30px;">
        <?php foreach($products as $product): ?>
    <?php if($product['product_kind'] == 'fabric'): ?>
        <div class="product-card">
            <a href="Help.html" class="help-circle-link">?</a>

                <a href="ProductDetails.php?id=<?php echo $product['product_id']; ?>"style="text-decoration: none; color: inherit;">
                <img src="images/Fabric/<?php echo $product['image_filename']; ?>" width="180" style="border-radius: 25px;">
                <h3 style="color: #1B263B;"><?php echo $product['name']; ?></h3>
            </a>

            <div style="font-size: 13px; text-align: left; padding: 0 10px; margin: 10px 0;">
                <p>• <b>Origin:</b> <?php echo $product['origin_country']; ?></p>
                <p>• <b>Length:</b> 3.5 Meters</p>
                <p>• <b>Texture:</b> <?php echo $product['weave_type']; ?></p>
            </div>

            <p style="color: #B59A5D; font-size: 18px;"><b><?php echo $product['price']; ?> SAR</b></p>

            <p style="font-size: 12px; color: #A8A9AD; margin-top: -8px;">Stock: <?php echo $product['stock_qty']; ?> left</p>

        <form method="POST" action="add_to_cart.php">

        <input type="hidden" name="variant_id" value="<?php echo $product['variant_id']; ?>">

        <p style="font-size: 12px;">Qty: <input type="number" name="qty" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" style="width: 45px; border-radius: 10px; border: 1px solid #A8A9AD;"></p>

        <button type="submit" name="add_to_cart" class="product-buy-button">Add to Cart</button>

        </form>
        </div>
    <?php endif; ?>
<?php endforeach; ?>
    </div>

    <div class="section-title-box" id="shmaq">
        <h2 class="title-underline">Shemagh Collection</h2>
    </div>

    <div style="text-align: center; margin-top: 30px;">

    <?php foreach($products as $product): ?>
        <?php if($product['product_kind'] == 'shemagh'): ?>
            <div class="product-card">

                <a href="Help.html" class="help-circle-link">?</a>

                <img src="images/Shemagh/<?php echo $product['image_filename']; ?>" width="180" style="border-radius: 25px;">

                <h3 style="color: #1B263B;">
                    <?php echo $product['name']; ?>
                </h3>

                <div style="font-size: 13px; text-align: left; padding: 0 10px; margin: 10px 0;">
                    <p>• <b>Origin:</b> <?php echo $product['origin_country']; ?></p>
                    <p>• <b>Material:</b> <?php echo $product['weave_type']; ?></p>
                </div>

                <p style="color: #B59A5D; font-size: 18px;">
                    <b><?php echo $product['price']; ?> SAR</b>
                </p>

                <p style="font-size: 12px; color: #A8A9AD; margin-top: -8px;">
                    Stock: <?php echo $product['stock_qty']; ?> left
                </p>

                

                <form method="POST" action="add_to_cart.php">

                    <input type="hidden" name="variant_id" value="<?php echo $product['variant_id']; ?>">

                    <p style="font-size: 12px;">Qty: <input type="number" name="qty" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" style="width: 45px; border-radius: 10px; border: 1px solid #A8A9AD;"></p>

                    <button type="submit" name="add_to_cart" class="product-buy-button">Add to Cart</button>

                </form>

            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    </div>

    <div class="contact-section-box" id="contact">
        <h2 style="color: #B59A5D;">Contact Us</h2>
        <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="text-align: left; min-width: 200px;">
                <p>📍 IAU University, Dammam</p>
                <p>📞 +966 500 000 000</p>
            </div>
            <a href="https://www.google.com/maps/place/%D9%83%D9%84%D9%8A%D8%A9+%D8%B9%D9%84%D9%88%D9%85+%D8%A7%D9%84%D8%AD%D8%A7%D8%B3%D8%A8+%D9%88%D8%AA%D9%82%D9%86%D9%8A%D8%A9+%D8%A7%D9%84%D9%85%D8%B9%D9%84%D9%88%D9%85%D8%A7%D8%AA%E2%80%AD/@26.3813435,50.1989765,14z/data=!4m6!3m5!1s0x3e49ef811304efab:0xe664343a49ebbf2b!8m2!3d26.3948879!4d50.1957014!16s%2Fg%2F1q5bxv2b_?entry=ttu&g_ep=EgoyMDI2MDMwOS4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                <img src="images/Location.png" alt="Map" width="280" style="border-radius: 25px; cursor: pointer;">
            </a>
        </div>
        <p style="margin-top: 25px; font-size: 12px; color: #888;">© 2026 Royal Weave - Section 8MS3</p>
    </div>
</body>
</html>