<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['User_ID'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM User WHERE User_ID = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("UPDATE User SET 
        User_Fname = :fname, 
        User_Lname = :lname, 
        User_Email = :email, 
        User_TelNo = :phone 
        WHERE User_ID = :id");

    $stmt->execute([
        'fname' => $_POST['first_name'],
        'lname' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'id' => $userId
    ]);

    // Update session variables if needed
    $_SESSION['User_Fname'] = $_POST['first_name'];
    $_SESSION['User_Lname'] = $_POST['last_name'];

    header("Location: admin-profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header class="navbar">
        <a href="admin.php" class="navbar_title2"><h1>taskly</h1></a>
        <a href="logout.php" class="navbar-index">Log out</a>
    </header>

    <div class="container">
        <nav class="sidebar">
            <div class="user-profile">
                <a href="admin-profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
                <h4><?= htmlspecialchars($_SESSION['User_Fname']) ?></h4>
            </div>
            <ul>
                <li><a href="admin-tasks.php">Manage Group Tasks</a></li>
                <li><a href="admin-group.php">Manage Groups</a></li>
                <li><a href="admin-task_personal.php">Manage Your Tasks</a></li>
            </ul>
        </nav>

        <section class="main-content">
            <div class="profile-page_content">
                <h2>Profile Information</h2>
                <div class="profile-page_section">
                <form method="POST" class="profile-page_form">
                    <label class="profile-page__label">First Name:</label>
                    <input type="text" name="first_name" class="profile-page__input" value="<?= htmlspecialchars($user['User_Fname']) ?>" required>

                    <label class="profile-page__label">Last Name:</label>
                    <input type="text" name="last_name" class="profile-page__input" value="<?= htmlspecialchars($user['User_Lname']) ?>" required>

                    <label class="profile-page__label">Email:</label>
                    <input type="email" name="email" class="profile-page__input" value="<?= htmlspecialchars($user['User_Email']) ?>" required>

                    <label class="profile-page__label">Phone:</label>
                    <input type="text" name="phone" class="profile-page__input" value="<?= htmlspecialchars($user['User_TelNo']) ?>">

                    <button type="submit" class="btn btn--update">Update Profile</button>
                </form>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <p class="footer__text">
            <a href="#">About</a> | <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Use</a> | <a href="#">Contact Us</a>
        </p>
    </footer>
</body>
</html>
