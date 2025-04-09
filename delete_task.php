<?php
session_start();
include('db.php');

if (isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Only allow deleting if user owns the task list
    $query = "DELETE FROM Task WHERE Task_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$taskId]);
}

header("Location: tasks.php");
exit();
?>
