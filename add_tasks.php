<?php
// Include the database connection
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $taskTitle = trim($_POST['taskTitle']);
    $taskDescription = trim($_POST['taskDescription']);
    $taskDeadline = $_POST['taskDeadline'];
    
    // Check if list_id exists in URL
    if (!isset($_GET['list_id']) || empty($_GET['list_id'])) {
        die("Error: Task list ID is missing.");
    }

    $listId = (int) $_GET['list_id']; // Sanitize list ID

    if (!empty($listId)) {
        $stmt = $conn->prepare("INSERT INTO Task (Task_Title, Task_Description, Task_Deadline, List_ID) 
                                VALUES (:taskTitle, :taskDescription, :taskDeadline, :listId)");
        $stmt->bindParam(':taskTitle', $taskTitle);
        $stmt->bindParam(':taskDescription', $taskDescription);
        $stmt->bindParam(':taskDeadline', $taskDeadline);
        $stmt->bindParam(':listId', $listId);
        $stmt->execute();
    } else {
        die("Error: List ID is missing. Task cannot be added.");
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
</head>
<body>
    <h2>Create Task</h2>

    
    <form method="POST">
        <input type="text" name="taskTitle" placeholder="Task Title" required>
        <textarea name="taskDescription" placeholder="Task Description" required></textarea>
        <input type="date" name="taskDeadline" required>
        <button type="submit" name="createTask">Create Task</button>
    </form>
</body>
</html>
