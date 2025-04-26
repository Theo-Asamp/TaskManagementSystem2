<?php
session_start();
include('db.php');

if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['User_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];

    $check = $conn->prepare("SELECT 1 FROM User_Group WHERE User_ID = ? AND Group_ID = ?");
    $check->execute([$userId, $groupId]);

    if (!$check->fetch()) {
        $stmt = $conn->prepare("INSERT INTO User_Group (User_ID, Group_ID) VALUES (?, ?)");
        $stmt->execute([$userId, $groupId]);
    }

    header("Location: groups.php");
    exit();
}
?>
