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

    // Delete the user from the User_Group join table
    $stmt = $conn->prepare("DELETE FROM User_Group WHERE User_ID = ? AND Group_ID = ?");
    $stmt->execute([$userId, $groupId]);

    // Redirect back to the groups page
    header("Location: groups.php");
    exit();
} else {
    header("Location: groups.php");
    exit();
}
?>
