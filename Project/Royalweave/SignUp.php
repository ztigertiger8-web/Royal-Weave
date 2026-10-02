<?php
session_start();
include("db.php");

if(isset($_POST['signup']))
{
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['city'];
    $password = $_POST['password'];

    if(strlen($password) < 8)
    {
        echo "<script>alert('Password must be at least 8 characters');</script>";
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
        $stmt->execute([$email]);

        if($stmt->rowCount() > 0)
        {
            echo "<script>alert('Email already exists');</script>";
        }
        else
        {
            $stmt = $conn->prepare("INSERT INTO customer(full_name, email, password_hash, phone, address)
                                    VALUES(?,?,?,?,?)");
            $stmt->execute([$full_name, $email, $password, $phone, $address]);

            echo "<script>alert('Account created successfully'); window.location.href='Login.php';</script>";
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page-body">
    <img src="images/logo2.png" alt="Logo" width="150">
    <h1 style="margin-top: 20px;">Create New Account</h1>
    <div class="auth-main-box">
        <form method="POST">
            <p>Name:<br><input type="text" name="name" placeholder="Abdulmohsen" class="signup-input-field" required></p>
            <p>Email Address:<br><input type="email" name="email" placeholder="xyz@gmail.com"  class="signup-input-field" required></p>
            <p>Phone Number:<br><input type="tel" name="phone" placeholder="05xxxxxxxx"  class="signup-input-field" required></p>
            <p>Select City:<br> 
                <select name="city"  class="signup-select-field" required>
                    <option value="" disabled selected>Choose your city</option>
                    <option value="Dammam">Dammam</option>
                    <option value="Al-Khobar">Al-Khobar</option>
                    <option value="Riyadh">Riyadh</option>
                    <option value="Jeddah">Jeddah</option>
                </select>
            </p>
            <p>Password:<br>
                <input type="password" name="password" id="p1" class="auth-input-field" required>
                <span id="msg" style="color: red; font-size: 12px;"></span>
            </p>
            <div style="text-align: center; margin-top: 25px;">
                <input type="submit" name="signup" value="Complete Registration" class="complete-register-button">
            </div>
        </form>
    </div>
    <p style="margin-top: 25px;">
        Already have an account? <a href="Login.php" style="color: #1B263B; font-weight: bold; text-decoration: none;">Login here</a>
    </p>
</body>
</html>