<?php
session_start();
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['task_id'])) {
    header("Location: admin-task_personal.php");
    exit();
}

$taskId = $_GET['task_id'];
$error = '';

// Fetch task details
$stmt = $conn->prepare("SELECT * FROM Task WHERE Task_ID = ?");
$stmt->execute([$taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    $error = "Task not found.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['taskTitle'] ?? '';
    $desc = $_POST['taskDesc'] ?? '';
    $deadline = $_POST['taskDeadline'] ?? '';

    if (!$title || !$desc || !$deadline) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE Task SET Task_Title = ?, Task_Description = ?, Task_Deadline = ? WHERE Task_ID = ?");
        $stmt->execute([$title, $desc, $deadline, $taskId]);

        header("Location: admin-task_personal.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
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
            <a href="admin-profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
            <h4><?php echo $_SESSION['User_Fname']; ?></h4>
        </div>
        <ul>
            <li><a href="admin-tasks.php">Manage Group Tasks</a></li>
            <li><a href="admin-group.php">Manage Groups</a></li>
            <li><a href="admin-task_personal.php">Manage Your tasks</a></li>
        </ul>
    </nav>
    <section class="main-content">
        <div class="main-content-container">
        <h2>Edit Task</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($task): ?>
            <form method="POST" class="edit-task-form">
                <label>Task Title:</label>
                <input type="text" name="taskTitle" value="<?= htmlspecialchars($task['Task_Title']) ?>" required><br>

                <label>Task Description:</label>
                <textarea name="taskDesc" required><?= htmlspecialchars($task['Task_Description']) ?></textarea><br>

                <label>Deadline:</label>
                <input type="date" name="taskDeadline" value="<?= htmlspecialchars($task['Task_Deadline']) ?>" required><br>

                <button type="submit">Save Changes</button>
                <br>
                <a href="admin-task_personal.php">Cancel</a>
            </form>
        <?php endif; ?>
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
