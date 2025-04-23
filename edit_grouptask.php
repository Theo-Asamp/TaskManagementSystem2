<?php
session_start();
include('db.php');

if (!isset($_GET['task_id'])) {
    header("Location: admin-tasks.php");
    exit();
}

$taskId = $_GET['task_id'];

// Fetch task info
$stmt = $conn->prepare("SELECT * FROM Group_Task WHERE GroupTask_ID = ?");
$stmt->execute([$taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

// If form submitted, update the task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['GroupTask_Name'] ?? '';
    $desc = $_POST['GroupTask_Description'] ?? '';
    $status = $_POST['GroupTask_Status'] ?? 'Pending';

    $updateStmt = $conn->prepare("
        UPDATE Group_Task 
        SET GroupTask_Name = ?, GroupTask_Description = ?, GroupTask_Status = ?
        WHERE GroupTask_ID = ?
    ");
    $updateStmt->execute([$name, $desc, $status, $taskId]);

    header("Location: admin-tasks.php");
    exit();
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
    <a href="admin.php" class="navbar_title2"><h1>taskly</h1></a>
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
                <li><a href="admin-task_personal.php">Manage Your Tasks</a></li>
            </ul>
        </nav>

    <section class="main-content">
        <div class="main-content-container">
            <h2>Edit Group Task</h2>
            <?php if (!$task): ?>
                <p>Task not found.</p>
            <?php else: ?>
            <form method="POST">
                <label>Task Name:</label>
                <input type="text" name="GroupTask_Name" value="<?= htmlspecialchars($task['GroupTask_Name']) ?>" required>

                <label>Task Description:</label>
                <textarea name="GroupTask_Description" required><?= htmlspecialchars($task['GroupTask_Description']) ?></textarea>

                <label>Status:</label>
                <select name="GroupTask_Status">
                    <option value="Pending" <?= $task['GroupTask_Status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $task['GroupTask_Status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Complete" <?= $task['GroupTask_Status'] === 'Complete' ? 'selected' : '' ?>>Complete</option>
                </select>

                <br><br>
                <button type="submit">Update Task</button>
                <br>
                <a href="admin-tasks.php">Cancel</a>
            </form>
        </div>
    <?php endif; ?>
    </section>
</div>
</body>
</html>
