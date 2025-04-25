<?php
session_start();
include('db.php');

// Set user ID (from session)
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

// Handle create task
if (isset($_POST['createTask'])) {
    $name = $_POST['taskName'] ?? '';
    $desc = $_POST['taskDesc'] ?? '';
    $deadline = $_POST['taskDeadline'] ?? ''; 
    $listId = $_POST['list_id'] ?? null;

    $stmt = $conn->prepare("INSERT INTO Task (Task_Title, Task_Description, Task_Deadline, List_ID) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $deadline, $listId]); 

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
    <style>
        .task-checkbox {
            display: flex;
            align-items: start;
            gap: 10px;
            margin-bottom: 10px;
        }

        .complete-checkbox {
            transform: scale(1.3);
            cursor: pointer;
            margin-top: 6px;
        }

        .task-content.checked h4,
        .task-content.checked p {
            text-decoration: line-through;
            color: gray;
        }
    </style>
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

    <section class="task-content">
        <div class="task-container">
            <h2>My Tasks</h2>

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
                                <a href="edit_tasklist.php?list_id=<?= $taskList['List_ID'] ?>">Edit Task List</a>
                                <form method="POST" class="add-tasklist_form">
                                    <input type="text" name="taskName" placeholder="Task Name" required>
                                    <input type="text" name="taskDesc" placeholder="Task Description" required>
                                    <input type="date" name="taskDeadline" required>
                                    <input type="hidden" name="list_id" value="<?= $taskList['List_ID'] ?>">
                                    <button type="submit" name="createTask">Add Task</button>
                                </form>

                                <form method="POST" action="delete_tasklist.php" style="display:inline;">
                                    <input type="hidden" name="list_id" value="<?= $taskList['List_ID'] ?>">
                                    <button type="submit" onclick="return confirm('Delete this task list and all its tasks?')">Delete List</button>
                                </form>
                            </div>

                            <!-- Tasks for this list -->
                            <div class="tasks-view">
                                <?php if (!isset($allTasks[$taskList['List_ID']])): ?>
                                    <p>No tasks for this list.</p>
                                <?php else: ?>
                                    <?php foreach ($allTasks[$taskList['List_ID']] as $task): ?>
                                        <div class="task-item">
                                            <label class="task-checkbox">
                                                <input type="checkbox" class="complete-checkbox">
                                                <span class="task-content">
                                                    <h4><?= htmlspecialchars($task['Task_Title']) ?></h4>
                                                    <p><?= htmlspecialchars($task['Task_Description']) ?></p>
                                                    <p><strong>Deadline:</strong> <?= htmlspecialchars($task['Task_Deadline']) ?></p>
                                                </span>
                                            </label>
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

<script>
    document.querySelectorAll('.complete-checkbox').forEach(box => {
        box.addEventListener('change', () => {
            const content = box.closest('.task-checkbox').querySelector('.task-content');
            content.classList.toggle('checked', box.checked);
        });
    });
</script>
</body>
</html>
