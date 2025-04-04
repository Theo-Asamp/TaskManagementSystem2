<?php
session_start();
include('db.php');

$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['name'])) {
    die("No task list name provided.");
}

$taskListName = $_GET['name'];

// Fetch task list by name
$stmt = $conn->prepare("SELECT * FROM Task_List WHERE TaskList_Name = ? AND User_ID = ?");
$stmt->execute([$taskListName, $userId]);
$taskList = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$taskList) {
    die("Task list not found.");
}

// Update task list
if (isset($_POST['updateTaskList'])) {
    $newName = $_POST['taskListName'];
    $newDesc = $_POST['taskListDesc'];

    $stmt = $conn->prepare("UPDATE Task_List SET TaskList_Name = ?, TaskList_Description = ? WHERE TaskList_Name = ? AND User_ID = ?");
    $stmt->execute([$newName, $newDesc, $taskListName, $userId]);

    header("Location: tasks.php");
    exit();
}
?>

<!-- Edit form -->
<form method="POST">
    <input type="text" name="taskListName" value="<?= htmlspecialchars($taskList['TaskList_Name']) ?>" required>
    <input type="text" name="taskListDesc" value="<?= htmlspecialchars($taskList['TaskList_Description']) ?>" required>
    <button type="submit" name="updateTaskList">Save Changes</button>
</form>
