<?php
session_start();
include("db.php");

if(isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Admin Login
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if($admin && $password == $admin['password_hash'])
    {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        if(isset($_POST['remember_me']))
        {
            setcookie(
                "remembered_email",
                $email,
                time() + (86400 * 30),
                "/"
            );
        }
        header("Location: /Royalweave/Admin.php");
        exit();
    }

    // Customer Login
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch();

    if($customer && $password == $customer['password_hash'])
    {
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['full_name'];
        if(isset($_POST['remember_me']))
        {
            setcookie(
                "remembered_email",
                $email,
                time() + (86400 * 30),
                "/"
            );
        }
        header("Location: /Royalweave/MainFrame.php");
        exit();
    }

    echo "<script>alert('Wrong Email or Password');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page-body">
    <img src="images/logo2.png" alt="logo" width="150">
    <h1>Login</h1>
    <div class="auth-main-box">
        <form method="POST">
            <p>Enter Your Email: <br>
            <input type="email" id="em" name="email" placeholder="xyz@gmail.com" class="auth-input-field" value="<?php echo isset($_COOKIE['remembered_email']) ? htmlspecialchars($_COOKIE['remembered_email']) : ''; ?>" required>            </p>
            <p>Enter Your Password: <br><input type="password" name="password" placeholder="*************" class="auth-input-field" required></p>
            <p><input type="checkbox" name="remember_me">Remember Me</p>
            <div style="margin-top: 25px; text-align: center;">
                <button type="submit" name="login" class="customer-login-button" style="width: 200px; display: block; margin: 0 auto 10px auto;">Log In</button>
                <a href="MainFrame.php" style="text-decoration: none; color: #888; font-size: 14px;">Continue as Visitor</a>
            </div>
        </form>
    </div>
    <p style="margin-top: 20px;">Don't have an account yet? <a href="SignUp.php" style="color: #1B263B; font-weight: bold; text-decoration: none;">Create an account</a></p>
</body>
</html>