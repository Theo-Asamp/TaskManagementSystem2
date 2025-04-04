<?php
session_start();
include('db.php');

$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['tasklist']) || empty($_GET['tasklist'])) {
    die("Task list name missing.");
}

$taskListName = $_GET['tasklist'];

// Get the List_ID from TaskList_Name
$stmt = $conn->prepare("SELECT List_ID FROM Task_List WHERE TaskList_Name = ? AND User_ID = ?");
$stmt->execute([$taskListName, $userId]);
$taskList = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$taskList) {
    die("Task list not found.");
}

$listId = $taskList['List_ID'];

if (isset($_POST['createTask'])) {
    $title = $_POST['taskTitle'];
    $desc = $_POST['taskDesc'];
    $deadline = $_POST['taskDeadline'];

    $stmt = $conn->prepare("INSERT INTO Task (Task_Title, Task_Description, Task_Deadline, List_ID) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $desc, $deadline, $listId]);

    header("Location: tasks.php");
    exit();
}
?>

<!-- Add Task Form -->
<form method="POST">
    <input type="text" name="taskTitle" placeholder="Task Title" required>
    <input type="text" name="taskDesc" placeholder="Task Description" required>
    <input type="date" name="taskDeadline" required>
    <button type="submit" name="createTask">Create Task</button>
</form>
