<?php
session_start();
include('db.php');

// Set user ID (replace with session later)
$userId = $_SESSION['user_id'] ?? 1;

// Handle create task list
if (isset($_POST['createTaskList'])) {
    $name = $_POST['taskListName'] ?? '';
    $desc = $_POST['taskListDesc'] ?? '';

    $stmt = $conn->prepare("INSERT INTO Task_List (TaskList_Name, TaskList_Description, User_ID) VALUES (?, ?, ?)");
    $stmt->execute([$name, $desc, $userId]);
    header("Location: tasks.php");
    exit();
}

// Handle delete task list
if (isset($_POST['deleteTaskList'])) {
    $listId = $_POST['deleteTaskList'];
    // Optionally delete tasks inside the list first if ON DELETE CASCADE isn't set
    $stmt = $conn->prepare("DELETE FROM Task WHERE List_ID = ?");
    $stmt->execute([$listId]);

    $stmt = $conn->prepare("DELETE FROM Task_List WHERE List_ID = ? AND User_ID = ?");
    $stmt->execute([$listId, $userId]);

    header("Location: tasks.php");
    exit();
}

// Get all task lists for the user
$stmt = $conn->prepare("SELECT List_ID, TaskList_Name, TaskList_Description FROM Task_List WHERE User_ID = ?");
$stmt->execute([$userId]);
$taskLists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get tasks for all lists
$allTasks = [];
if ($taskLists) {
    $stmt = $conn->prepare("
        SELECT Task_ID, Task_Title, Task_Description, Task_Deadline, List_ID 
        FROM Task WHERE List_ID IN (
            SELECT List_ID FROM Task_List WHERE User_ID = ?
        )
    ");
    $stmt->execute([$userId]);
    while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $allTasks[$task['List_ID']][] = $task;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks</title>
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
                <h4>User</h4>
            </div>
            <ul>
                <li><a href="tasks.php">Tasks</a></li>
                <li><a href="groups.php">Groups</a></li>
            </ul>
        </nav>

        <section class="task-content">
            <div class="task-container">
                <h2>My Task Lists</h2>

                <!-- Add Task List -->
                <form method="POST" class="add-tasklist_form">
                    <input type="text" name="taskListName" placeholder="Task List Name" required>
                    <input type="text" name="taskListDesc" placeholder="Task List Description" required>
                    <button type="submit" name="createTaskList">Create Task List</button>
                </form>

                <div class="tasklist-view">
                    <?php if (!$taskLists): ?>
                        <p>No task lists found.</p>
                    <?php else: ?>
                        <?php foreach ($taskLists as $taskList): ?>
                            <div class="task-list-item">
                                <h3><?= htmlspecialchars($taskList['TaskList_Name']) ?></h3>
                                <p><?= htmlspecialchars($taskList['TaskList_Description']) ?></p>

                                <div class="tasklist-actions">
                                    <a href="edit_tasklist.php?name=<?= urlencode($taskList['TaskList_Name']) ?>">Edit Task List</a>
                                    <a href="add_task.php?tasklist=<?= urlencode($taskList['TaskList_Name']) ?>">Add Task</a>
                                    <a href="delete_tasklist.php?name=<?= urlencode($taskList['TaskList_Name']) ?>" onclick="return confirm('Delete this task list and all its tasks?')">Delete</a>

                                </div>

                                <!-- Tasks for this list -->
                                <div class="tasks-view">
                                    <?php if (!isset($allTasks[$taskList['List_ID']])): ?>
                                        <p>No tasks for this list.</p>
                                    <?php else: ?>
                                        <?php foreach ($allTasks[$taskList['List_ID']] as $task): ?>
                                            <div class="task-item">
                                                <h4><?= htmlspecialchars($task['Task_Title']) ?></h4>
                                                <p><?= htmlspecialchars($task['Task_Description']) ?></p>
                                                <p><strong>Deadline:</strong> <?= htmlspecialchars($task['Task_Deadline']) ?></p>
                                                <a href="edit_task.php?task_id=<?= $task['Task_ID'] ?>">Edit</a>
                                                <a href="delete_task.php?task_id=<?= $task['Task_ID'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
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
