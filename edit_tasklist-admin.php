<?php
session_start();
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

// Validate list_id from query
if (!isset($_GET['list_id']) || !is_numeric($_GET['list_id'])) {
    die("No task list ID provided.");
}

$listId = $_GET['list_id'];

// Fetch task list by ID and check ownership
$stmt = $conn->prepare("SELECT * FROM Task_List WHERE List_ID = ? AND User_ID = ?");
$stmt->execute([$listId, $userId]);
$taskList = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$taskList) {
    die("Task list not found or unauthorized access.");
}
  
// Handle update
if (isset($_POST['updateTaskList'])) {
    $newName = $_POST['taskListName'] ?? '';
    $newDesc = $_POST['taskListDesc'] ?? '';

    if (!empty($newName) && !empty($newDesc)) {
        $stmt = $conn->prepare("UPDATE Task_List SET TaskList_Name = ?, TaskList_Description = ? WHERE List_ID = ? AND User_ID = ?");
        $stmt->execute([$newName, $newDesc, $listId, $userId]);

        header("Location: admin-task_personal.php");
        exit();
    } else {
        $error = "Both name and description are required.";
    }
}
?>

<!-- Edit form -->
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
            <h2>Edit Task List</h2>

            <?php if (isset($error)) : ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Task Name:</label>
                <input type="text" name="taskListName" value="<?= htmlspecialchars($taskList['TaskList_Name']) ?>" required><br>
                <label>Task Description:</label>
                <input type="text" name="taskListDesc" value="<?= htmlspecialchars($taskList['TaskList_Description']) ?>" required><br>
                <button type="submit" name="updateTaskList">Save Changes</button>
                <br>
                <a href="admin-task_personal.php">Cancel</a>
            </form>
        </div>
    </section>
</div>
</body>
</html>
