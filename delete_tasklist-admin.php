<?php
session_start();
include('db.php');

$userId = $_SESSION['user_id'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_id'])) {
    $listId = $_POST['list_id'];

    // Delete tasks first (if cascade is not set)
    $stmt = $conn->prepare("DELETE FROM Task WHERE List_ID = ?");
    $stmt->execute([$listId]);

    // Delete the list
    $stmt = $conn->prepare("DELETE FROM Task_List WHERE List_ID = ? AND User_ID = ?");
    $stmt->execute([$listId, $userId]);
}

header("Location: admin-task_personal.php");
exit();
