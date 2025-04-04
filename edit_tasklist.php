<?php
include('db.php');

// Validate list_id
$listId = filter_input(INPUT_GET, 'list_id', FILTER_VALIDATE_INT);
if (!$listId) {
    header('Location: tasks.php');
    exit();
}

// Fetch task list
$stmt = $conn->prepare("SELECT TaskList_Name, TaskList_Description FROM Task_List WHERE List_ID = :list_id");
$stmt->bindParam(':list_id', $listId, PDO::PARAM_INT);
$stmt->execute();
$taskList = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if the task list does not exist
if (!$taskList) {
    header('Location: tasks.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskListName = trim($_POST['taskListName']);
    $taskListDesc = trim($_POST['taskListDesc']);

    $stmt = $conn->prepare("UPDATE Task_List SET TaskList_Name = :name, TaskList_Description = :desc WHERE List_ID = :list_id");
    $stmt->execute([
        ':name' => $taskListName,
        ':desc' => $taskListDesc,
        ':list_id' => $listId
    ]);

    header('Location: tasks.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task List</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <h2>Edit Task List</h2>
    <form method="POST">
        <label>Task List Name</label>
        <input type="text" name="taskListName" value="<?= htmlspecialchars($taskList['TaskList_Name']) ?>" required>

        <label>Task List Description</label>
        <input type="text" name="taskListDesc" value="<?= htmlspecialchars($taskList['TaskList_Description']) ?>" required>

        <button type="submit">Save Changes</button>
        <a href="tasks.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
