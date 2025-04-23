<?php
session_start();
include('db.php');

// Set user ID (from session)
$userId = $_SESSION['user_id'] ?? 1;

if (!isset($_SESSION['User_ID'])) {
    die("You must be logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['User_ID'];

    // Check if already a member
    $check = $conn->prepare("SELECT 1 FROM User_Group WHERE User_ID = ? AND Group_ID = ?");
    $check->execute([$user_id, $group_id]);

    if ($check->rowCount() === 0) {
        $stmt = $conn->prepare("INSERT INTO User_Group (User_ID, Group_ID) VALUES (?, ?)");
        $stmt->execute([$user_id, $group_id]);
    }

    // Redirect back to groups page
    header("Location: groups.php");
    exit();
}
?>
