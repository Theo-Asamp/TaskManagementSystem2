<?php
session_start();
include('db.php');

// Fetch user task lists
$userId = 1; // Change this to use $_SESSION['user_id'] when authentication is implemented

// Handle delete task list request
if (isset($_POST['deleteTaskList'])) {
    $deleteListId = $_POST['deleteTaskList'];
    $stmt = $conn->prepare("DELETE FROM Task_List WHERE List_ID = :listId AND User_ID = :userId");
    $stmt->bindParam(':listId', $deleteListId);
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    header("Location: tasks.php");
    exit();
}

$query = "SELECT List_ID, TaskList_Name, TaskList_Description FROM Task_List WHERE User_ID = :userId";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$taskLists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tasks grouped by task list
$allTasks = [];
$query = "SELECT Task_ID, Task_Title, Task_Description, Task_Deadline, List_ID FROM Task WHERE List_ID IN (SELECT List_ID FROM Task_List WHERE User_ID = :userId)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
while ($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $allTasks[$task['List_ID']][] = $task;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <li><a href="tasks.php"><span>Tasks</span></a></li> 
                <li><a href="groups.php"><span>Groups</span></a></li>
            </ul>
        </nav>
        
        <section class="task-content">
            <div class="task-container">
                <h2>My Task Lists</h2>
                <p>Create a task list to get started</p>
                
                <div class="add-tasklist">
                    <form method="POST" class="add-tasklist_form">
                        <input type="text" name="taskListName" placeholder="Task List Name" required>
                        <input type="text" name="taskListDesc" placeholder="Task List Description" required>
                        <button type="submit" name="createTaskList">Create Task List</button>
                    </form>
                </div>
                
                <div class="tasklist-view">
                    <?php if (!$taskLists): ?>
                        <p>No task lists found for this user.</p>
                    <?php else: ?>
                        <?php foreach ($taskLists as $taskList): ?>
                            <div class="task-list-item">
                                <h4><?= htmlspecialchars($taskList['TaskList_Name']) ?></h4>
                                <p><?= htmlspecialchars($taskList['TaskList_Description']) ?></p>
                                <a href="edit_tasklist.php?list_id=<?= $taskList['List_ID'] ?>">Edit Task List</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="deleteTaskList" value="<?= $taskList['List_ID'] ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this task list?');">Delete</button>
                                </form>
                                <a href="add_task.php?list_id=<?= $taskList['List_ID'] ?>">Add Task</a>
                                <div class="tasks-view">
                                    <?php if (!isset($allTasks[$taskList['List_ID']])): ?>
                                        <p>No tasks available for this list.</p>
                                    <?php else: ?>
                                        <?php foreach ($allTasks[$taskList['List_ID']] as $task): ?>
                                            <div class="task-item">
                                                <h4><?= htmlspecialchars($task['Task_Title']) ?></h4>
                                                <p><?= htmlspecialchars($task['Task_Description']) ?></p>
                                                <p><strong>Deadline:</strong> <?= htmlspecialchars($task['Task_Deadline']) ?></p>
                                                <a href="edit_task.php?task_id=<?= $task['Task_ID'] ?>">Edit Task</a>
                                                <a href="delete_task.php?task_id=<?= $task['Task_ID'] ?>">Delete Task</a>
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
