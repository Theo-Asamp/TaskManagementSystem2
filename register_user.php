<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $role = "employee"; // Default role

    if (!$email) {
        $error = "Invalid email!";
    } else {
        $stmt = $conn->prepare("SELECT User_ID FROM User WHERE User_Email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            $error = "Email already exists!";
        } else {
            $stmt = $conn->prepare("INSERT INTO User (User_Fname, User_Lname, User_Email, User_TelNo, User_Password, User_Role) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$firstName, $lastName, $email, $phone, password_hash($password, PASSWORD_DEFAULT), $role])) {
                $_SESSION = ['user_id' => $conn->lastInsertId(), 'user_role' => $role, 'user_fname' => $firstName, 'user_lname' => $lastName];
                header("Location: dashboard.php");
                exit();
            }
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Employee</title>
    <link rel="stylesheet" href="/css/global.css">
</head>
<body>
    <header class="navbar"><h1 class="navbar_title">taskly</h1></header>
    <div class="register-container">
        <div class="register-section">
            <h2 class="register-title">Register as Employee</h2>
            <form action="" method="POST" class="login-form">
                <input type="text" name="first_name" placeholder="First Name*" required>
                <input type="text" name="last_name" placeholder="Last Name*" required>
                <input type="text" name="phone" placeholder="Phone">
                <input type="email" name="email" placeholder="Email*" required>
                <input type="password" name="password" placeholder="Password*" required>
                <button type="submit" class="btn btn--register">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
