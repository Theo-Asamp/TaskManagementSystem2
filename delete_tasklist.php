<?php
session_start();
include('db.php');

$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_GET['name']) || empty($_GET['name'])) {
    die("❌ No task list name provided.");
}

$taskListName = trim($_GET['name']);

// Get List_ID from name
$stmt = $conn->prepare("SELECT List_ID FROM Task_List WHERE TaskList_Name = ? AND User_ID = ?");
$stmt->execute([$taskListName, $userId]);
$taskList = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$taskList) {
    die("❌ Task list not found.");
}

$listId = $taskList['List_ID'];

// Delete tasks first (optional unless you're using ON DELETE CASCADE)
$stmt = $conn->prepare("DELETE FROM Task WHERE List_ID = ?");
$stmt->execute([$listId]);

// Delete the list
$stmt = $conn->prepare("DELETE FROM Task_List WHERE List_ID = ? AND User_ID = ?");
$stmt->execute([$listId, $userId]);

header("Location: tasks.php");
exit();
?>
