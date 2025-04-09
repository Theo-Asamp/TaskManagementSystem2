<?php
session_start();
include 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $sql = "SELECT User_ID, User_Fname, User_Lname, User_Password, User_Role FROM User WHERE User_Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $email);
    $stmt->execute();

    // Check if user exists
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Verify the password
        if (password_verify($password, $user['User_Password'])) {
            // Set session variables
            $_SESSION['User_ID'] = $user['User_ID'];
            $_SESSION['User_Fname'] = $user['User_Fname'];
            $_SESSION['User_Lname'] = $user['User_Lname'];
            $_SESSION['User_Role'] = $user['User_Role'];

            // Redirect based on user role
            if ($user['User_Role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php"); 
            }
            exit();
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header class="navbar">
        <h1 class="navbar_title">taskly</h1>
    </header>

    <div class="login-container">
        <div class="login-section">
            <h2>Welcome to taskly</h2>
            <p>To get started please sign in</p>

            <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

            <form class="login-form" method="POST">
                <label for="email">Email<span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" required>

                <label for="password">Password<span style="color: red;">*</span></label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn btn--login">Sign In</button>
            </form>

            <div class="or-text">Or</div>
            <a href="register.php"><button class="btn btn--register">Register</button></a>
        </div>
    </div>
</body>
</html>
