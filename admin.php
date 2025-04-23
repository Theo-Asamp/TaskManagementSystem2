<?php
session_start(); // Start the session to access session variables
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_SESSION['User_Fname'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
// Check if the session variable for User_Fname is set
$User_Fname = isset($_SESSION['User_Fname']) ? $_SESSION['User_Fname'] : 'Guest'; // Default to 'Guest' if not set

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
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
                <h4><?php echo $_SESSION['User_Fname']; ?></h4> 
                <p>Admin</p>
            </div>
            <ul>
                <li><a href="admin-tasks.php"><i class="sidebar-texts"></i> <span>Manage Group Tasks</span></a></li> 
                <li><a href="admin-group.php"><i class="sidebar-text"></i> <span>Manage Groups</span></a></li>
                <li><a href="admin-task_personal.php">Manage Your Tasks</a></li>

            </ul>
        </nav>

        <section class="main-content">
            <div class="home-box">
                <p><span id="current-date"></span></p>
                <script>
                    document.getElementById("current-date").textContent = new Date().toLocaleDateString();
                </script>
                <h1>Welcome, <?php echo $_SESSION['User_Fname']; ?></h1>
                <p>Here you can manage your tasks professionally.</p>
                <a href="admin-group.php"><button class="manage-tasks-btn">Manage Groups</button></a>
            </div>
        </section>
    </div>

</body>
<footer class="footer">
    <p class="footer__text">
        <a>About</a> | <a>Privacy Policy</a> |
        <a>Terms of Use</a> | <a>Contact Us</a>
    </p>
</footer>
</html>
