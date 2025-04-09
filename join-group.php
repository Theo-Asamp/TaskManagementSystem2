<?php
session_start();
include('db.php');

if (!isset($_SESSION['User_ID'])) {
    die("You must be logged in.");
}

if (isset($_POST['join_group'])) {
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['User_ID'];

    // Check if already a member
    $check = $conn->prepare("SELECT * FROM Group_Members WHERE User_ID = ? AND Group_ID = ?");
    $check->execute([$user_id, $group_id]);

    if ($check->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO Group_Members (User_ID, Group_ID) VALUES (?, ?)");
        $stmt->execute([$user_id, $group_id]);
        echo "Successfully joined the group!";
    } else {
        echo "You're already a member of this group.";
    }
}
?>
