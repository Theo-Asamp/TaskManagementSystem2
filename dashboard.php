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
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header class="navbar">
        <a href="dashboard.php" class="navbar_title2"><h1>taskly</h1></a>
        <a class="navbar-index" href="logout.php">Log out</a>
    </header>

    <div class="container">
        <nav class="sidebar">
            <div class="user-profile">
                <a href="profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
                <h4><?= htmlspecialchars($User_Fname ) ?></h4> 
            </div>
            <ul>
                <li><a href="tasks.php"><i class="sidebar-texts"></i> <span>Tasks</span></a></li> 
                <li><a href="groups.php"><i class="sidebar-text"></i> <span>Groups</span></a></li>
            </ul>
        </nav>

        <section class="main-content">
            <div class="home-box">
                <p><span id="current-date"></span></p>
                <script>
                    const today = new Date();
                    document.getElementById("current-date").textContent = today.toLocaleDateString();
                </script>
                <h1>Welcome, <?= htmlspecialchars($User_Fname) ?>!</h1>
                <p>Organize your tasks efficiently and improve productivity.</p>
                <a href="tasks.php"><button class="manage-tasks-btn">Manage tasks</button></a>
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
