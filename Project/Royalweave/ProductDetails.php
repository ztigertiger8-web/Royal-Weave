<?php
session_start();
include("db.php");

if(!isset($_GET['id']))
{
    header("Location: MainFrame.php");
    exit();
}

$product_id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        p.product_id,
        p.name,
        p.description,
        p.product_kind,

        v.variant_id,
        v.price,
        v.stock_qty,
        v.origin_country,
        v.weave_type,
        v.image_filename

    FROM product p

    JOIN product_variant v
        ON p.product_id = v.product_id

    WHERE p.product_id = ?
");

$stmt->execute([$product_id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product)
{
    header("Location: MainFrame.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="main-page-body">

    <div class="header-center" style="margin-bottom: 20px;">
        <img src="images/logo2.png" alt="Logo" width="120">
        <h1 style="color: #1B263B;">Product Details</h1>
    </div>

    <div style="max-width: 980px; margin: auto; background: white; border: 2px solid #A8A9AD; border-radius: 35px; padding: 35px; box-shadow: 6px 6px 0px #eeeeee;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 30px;">
            <div style="flex: 1; min-width: 280px; text-align: center;">
                <img src="images/<?php echo $product['product_kind'] == 'fabric' ? 'Fabric' : 'Shemagh'; ?>/<?php echo $product['image_filename']; ?>" alt="Product Image" width="320" style="border-radius: 30px; max-width: 100%;">
            </div>
            <div style="flex: 1; min-width: 280px; color: #1B263B;">
                <h2><?php echo $product['name']; ?></h2>
                <p><b>Origin:</b> <?php echo $product['origin_country']; ?></p>
                <p style="font-size: 15px; line-height: 1.8; margin: 0 0 10px 0;"><b>Length:</b> 3.5 Meters</p>
                <p style="font-size: 15px; line-height: 1.8; margin: 0 0 10px 0;"><b>Texture:</b> <?php echo $product['weave_type']; ?></p>
                <p style="font-size: 15px; line-height: 1.8; margin: 0 0 10px 0;"><b>Description:</b> <?php echo $product['description']; ?></p>
                <p style="font-size: 26px; color: #B59A5D; font-weight: bold; margin: 20px 0 8px 0;"><?php echo $product['price']; ?> SAR</p>
                <p style="font-size: 13px; color: #A8A9AD; margin: 0 0 18px 0;">Stock: <?php echo $product['stock_qty']; ?> left</p>
                    <form method="POST" action="add_to_cart.php">

                        <input type="hidden"
                            name="variant_id"
                            value="<?php echo $product['variant_id']; ?>">

                        <p>Qty:<input type="number" name="qty" value="1" min="1" required style="width: 55px; border-radius: 10px; border: 1px solid #A8A9AD; padding: 6px;"></p>

                        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 10px;">

                            <button type="submit"
                                    name="add_to_cart"
                                    class="product-buy-button">
                                Add to cart
                            </button>

                            <a href="MainFrame.php" class="gray-button-link" style="text-decoration: none; display: inline-block;">Back to Store</a>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-section-box" style="max-width: 980px; margin-left: auto; margin-right: auto;" id="contact">
        <h2 style="color: #B59A5D;">Contact Us</h2>
        <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="text-align: left; min-width: 200px; color: #1B263B;">
                <p>📍 IAU University, Dammam</p>
                <p>📞 +966 500 000 000</p>
            </div>
            <a href="https://www.google.com/maps/place/%D9%83%D9%84%D9%8A%D8%A9+%D8%B9%D9%84%D9%88%D9%85+%D8%A7%D9%84%D8%AD%D8%A7%D8%B3%D8%A8+%D9%88%D8%AA%D9%82%D9%86%D9%8A%D8%A9+%D8%A7%D9%84%D9%85%D8%B9%D9%84%D9%88%D9%85%D8%A7%D8%AA%E2%80%AD/@26.3813435,50.1989765,14z/data=!4m6!3m5!1s0x3e49ef811304efab:0xe664343a49ebbf2b!8m2!3d26.3948879!4d50.1957014!16s%2Fg%2F1q5bxv2b_?entry=ttu&g_ep=EgoyMDI2MDMwOS4wIKXMDSoASAFQAw%3D%3D" target="_blank">
                <img src="images/Location.png" alt="Map" width="280" style="border-radius: 25px; cursor: pointer;">
            </a>
        </div>
    </div>

</body>
</html>
