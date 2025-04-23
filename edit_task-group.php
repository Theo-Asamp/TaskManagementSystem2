<?php
session_start();
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['group_task_id'])) {
    header("Location: admin-tasks.php");
    exit();
}

$groupTaskId = $_GET['group_task_id'];
$error = '';

// Fetch group task
$stmt = $conn->prepare("SELECT * FROM Group_Task WHERE GroupTask_ID = ?");
$stmt->execute([$groupTaskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    $error = "Group task not found.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['groupTaskName'] ?? '';
    $desc = $_POST['groupTaskDesc'] ?? '';
    $status = $_POST['groupTaskStatus'] ?? '';

    if (!$name || !$desc || !$status) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE Group_Task SET GroupTask_Name = ?, GroupTask_Description = ?, GroupTask_Status = ? WHERE GroupTask_ID = ?");
        $stmt->execute([$name, $desc, $status, $groupTaskId]);

        header("Location: admin-tasks.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Group Task</title>
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
        <h2>Edit Group Task</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($task): ?>
            <form method="POST" class="edit-task-form">
                <label>Task Name:</label>
                <input type="text" name="groupTaskName" value="<?= htmlspecialchars($task['GroupTask_Name']) ?>" required>

                <label>Description:</label>
                <textarea name="groupTaskDesc" required><?= htmlspecialchars($task['GroupTask_Description']) ?></textarea>

                <label>Status:</label>
                <select name="groupTaskStatus" required>
                    <option value="Pending" <?= $task['GroupTask_Status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $task['GroupTask_Status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $task['GroupTask_Status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>

                <button type="submit">Save Changes</button>
                <a href="admin-tasks.php">Cancel</a>
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
