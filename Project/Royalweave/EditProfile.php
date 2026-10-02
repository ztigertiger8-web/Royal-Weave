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
    SELECT *
    FROM customer
    WHERE customer_id = ?
");

$stmt->execute([$customer_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


if(isset($_POST['save']))
{
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if(!empty($_POST['password']))
    {
        $password = $_POST['password'];

        $stmt = $conn->prepare("
            UPDATE customer
            SET 
                full_name = ?,
                email = ?,
                phone = ?,
                address = ?,
                password_hash = ?
            WHERE customer_id = ?
        ");

        $stmt->execute([
            $full_name,
            $email,
            $phone,
            $address,
            $password,
            $customer_id
        ]);
    }
    else
    {
        $stmt = $conn->prepare("
            UPDATE customer
            SET 
                full_name = ?,
                email = ?,
                phone = ?,
                address = ?
            WHERE customer_id = ?
        ");

        $stmt->execute([
            $full_name,
            $email,
            $phone,
            $address,
            $customer_id
        ]);
    }

    header("Location: MainFrame.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="profile-page-body">

    <img src="images/logo2.png" alt="Logo" width="130" class="profile-main-logo">
    <h2 class="update-title">Update Your Information</h2>
    <p class="update-subtitle">Keep your contact details up to date for better service.</p>

    <div class="profile-card-box">
        <form method="POST">
            <p>Your Name:<br><input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" placeholder="Abdulmohsen"  class="text-input-style"></p>
            <p>Email Address:<br><input type="email" name="email" value="<?php echo $user['email']; ?>" placeholder="Abdulmohsen@iau.edu.sa"  class="text-input-style"></p>
            <p>Phone Number:<br><input type="tel" name="phone" value="<?php echo $user['phone']; ?>" placeholder="05xxxxxxxx"  class="text-input-style"></p>
            <p>Location (City):<br>
                <select name="address" class="text-input-style" required>
                    <?php
                    $cities = ["Dammam", "Al-Khobar", "Riyadh", "Jeddah"];
                    foreach($cities as $city)
                    {
                        $selected = ($user['address'] == $city) ? "selected" : "";

                        echo "<option value='$city' $selected>$city</option>";
                    }
                    ?>
                </select>
            </p>
            <p>New Password (Optional):<br><input type="password" name="password" placeholder="Leave empty to keep current" class="text-input-style"></p>

            <div class="save-button-area">
                <button type="submit" name="save" class="save-data-button">Save Changes</button>
            </div>
        </form>
    </div>

    <p class="bottom-security-text">Royal Weave - Security & Privacy Guaranteed</p>

</body>
</html>