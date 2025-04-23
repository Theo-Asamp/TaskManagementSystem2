<?php
session_start();
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

// Handle create group
if (isset($_POST['createGroup'])) {
    $name = $_POST['groupName'] ?? '';

    // Insert new group into the database
    $stmt = $conn->prepare("INSERT INTO GroupTable (Group_Name, User_ID) VALUES (?, ?)");
    $stmt->execute([$name, $userId]);

    header("Location: admin-tasks.php");
    exit();
}

// Get all groups for the admin
$stmt = $conn->prepare("SELECT Group_ID, Group_Name FROM GroupTable WHERE User_ID = ?");
$stmt->execute([$userId]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get tasks for all groups including descriptions
$allGroupTasks = [];
if ($groups) {
    $stmt = $conn->prepare("
        SELECT GroupTask_ID, GroupTask_Name, GroupTask_Status, GroupTask_Description, Group_ID 
        FROM Group_Task WHERE Group_ID IN (
            SELECT Group_ID FROM GroupTable WHERE User_ID = ?
        )
    ");
    $stmt->execute([$userId]);

    while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $allGroupTasks[$task['Group_ID']][] = $task;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Groups and Tasks</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
<header class="navbar">
    <a href="admin-dashboard.php" class="navbar_title2"><h1>taskly</h1></a>
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

    <section class="task-content">
    <div class="task-container">
        <h2>Manage Groups</h2>

        <div class="tasklist-view">
            <?php if (!$groups): ?>
                <p>No groups found.</p>
            <?php else: ?>
                <?php foreach ($groups as $group): ?>
                    <div class="group-panel">
                        <div class="group-header">
                            <h3><?= htmlspecialchars($group['Group_Name']) ?></h3>
                        </div>

                        <div class="tasks-view">
                            <?php if (empty($allGroupTasks[$group['Group_ID']])): ?>
                                <p>No tasks for this group.</p>
                            <?php else: ?>
                                <?php foreach ($allGroupTasks[$group['Group_ID']] as $task): ?>
                                    <div class="task-card">
                                        <div class="task-info">
                                            <h4><?= htmlspecialchars($task['GroupTask_Name']) ?></h4>
                                            <p><strong>Status:</strong> <?= htmlspecialchars($task['GroupTask_Status']) ?></p>
                                            <p><strong>Description:</strong> <?= htmlspecialchars($task['GroupTask_Description'] ?? 'No description.') ?></p>
                                        </div>
                                        <br>
                                        <div class="task-actions">
                                            <a href="edit_grouptask.php?task_id=<?= $task['GroupTask_ID'] ?>">Edit</a>
                                            <a href="delete_grouptask.php?GroupTask_ID=<?= $task['GroupTask_ID'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
