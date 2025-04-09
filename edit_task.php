<?php
session_start();
include('db.php');

if (!isset($_GET['task_id'])) {
    header("Location: tasks.php");
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

        header("Location: tasks.php");
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
            <a href="profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
            <h4><?php echo $_SESSION['User_Fname']; ?></h4>
        </div>
        <ul>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="groups.php">Groups</a></li>
        </ul>
    </nav>
    <section class="edit-task-section">
        <h2>Edit Task</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($task): ?>
            <form method="POST" class="edit-task-form">
                <label>Task Title:</label>
                <input type="text" name="taskTitle" value="<?= htmlspecialchars($task['Task_Title']) ?>" required>

                <label>Task Description:</label>
                <textarea name="taskDesc" required><?= htmlspecialchars($task['Task_Description']) ?></textarea>

                <label>Deadline:</label>
                <input type="date" name="taskDeadline" value="<?= htmlspecialchars($task['Task_Deadline']) ?>" required>

                <button type="submit">Save Changes</button>
                <a href="tasks.php">Cancel</a>
            </form>
        <?php endif; ?>
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
