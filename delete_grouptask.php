<?php
session_start();
include('db.php');

if (isset($_GET['GroupTask_ID'])) {
    $GroupTask_ID = $_GET['GroupTask_ID'];

    // Only allow deleting if user owns the task list
    $query = "DELETE FROM Group_Task WHERE GroupTask_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$GroupTask_ID]);
}

header("Location: admin-tasks.php");
exit();
?>