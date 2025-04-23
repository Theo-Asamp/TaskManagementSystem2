<?php
session_start();
include('db.php');  // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['User_ID'])) {
    header("Location: login.php");
    exit();
}

// Handle group creation
if (isset($_POST['create_group'])) {
    $stmt = $conn->prepare("INSERT INTO GroupTable (Group_Name, User_ID) VALUES (:group_name, :user_id)");
    $stmt->bindParam(':group_name', $_POST['group_name']);
    $stmt->bindParam(':user_id', $_SESSION['User_ID']);
    $stmt->execute();
    echo "Group created successfully!";
}

// Handle task addition
if (isset($_POST['add_task'])) {
    $stmt = $conn->prepare("INSERT INTO Group_Task (GroupTask_Name, GroupTask_Description, GroupTask_Status, Group_ID) 
                           VALUES (:task_title, :task_description, 'Pending', :group_id)");
    $stmt->bindParam(':task_title', $_POST['task_title']);
    $stmt->bindParam(':task_description', $_POST['task_description']);
    $stmt->bindParam(':group_id', $_POST['group_id']);
    $stmt->execute();
    echo "Task added successfully!";
}

// Handle group deletion
if (isset($_GET['delete_group_id'])) {
    $stmt = $conn->prepare("DELETE FROM GroupTable WHERE Group_ID = :group_id");
    $stmt->bindParam(':group_id', $_GET['delete_group_id']);
    $stmt->execute();
    echo "Group deleted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Groups</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header class="navbar">
        <a href="admin.php" class="navbar_title2"><h1>taskly</h1></a>
        <a href="logout.php" class="navbar-index">Log out</a>
    </header>

    <div class="container">
        <nav class="sidebar">
            <div class="user-profile">
                <a href="admin-profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
                <h4><?php echo $_SESSION['User_Fname']; ?></h4>
            </div>
            <ul>
                <li><a href="admin-tasks.php">Manage Group Tasks</a></li>
                <li><a href="admin-group.php">Manage Groups</a></li>
                <li><a href="admin-task_personal.php">Manage Your Tasks</a></li>
            </ul>
        </nav>

        <section class="main-content">
            <div class="main-content-container">
                <!-- Create Group Form -->
                <h2>Create Group</h2>
                <form action="admin-group.php" method="POST">
                    <label for="group_name">Group Name:</label>
                    <input type="text" name="group_name" id="group_name" required>             
                    <button type="submit" name="create_group">Create Group</button>
                </form>

                <hr>

                <!-- Add Task to Group Form -->
                <h2>Add Task to Group</h2>
                <form action="admin-group.php" method="POST">
                    <label for="group_id">Select Group:</label>
                    <select name="group_id" id="group_id">
                        <?php 
                        // Fetch groups for the select dropdown
                        $stmt = $conn->prepare("SELECT * FROM GroupTable WHERE User_ID = :user_id");
                        $stmt->bindParam(':user_id', $_SESSION['User_ID']);
                        $stmt->execute();
                        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($groups as $group) { 
                            echo "<option value='" . $group['Group_ID'] . "'>" . $group['Group_Name'] . "</option>"; 
                        } 
                        ?>
                    </select>

                    <label for="task_title">Task Title:</label>
                    <input type="text" name="task_title" required>

                    <label for="task_description">Task Description:</label>
                    <textarea name="task_description" required></textarea>

                    <button type="submit" name="add_task">Add Task</button>
                </form>

                <h2>Groups Overview</h2>
            <?php
            // Fetch groups the user is part of
            $stmt = $conn->prepare("SELECT * FROM GroupTable WHERE User_ID = :user_id");
            $stmt->bindParam(':user_id', $_SESSION['User_ID']);
            $stmt->execute();
            $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($groups as $group) {
                // Display the group
                echo "<div class='group-panel'>";
                echo "<p>Group Name: " . htmlspecialchars($group['Group_Name']) . " ";
                echo "<a href='admin-group.php?delete_group_id=" . $group['Group_ID'] . "'>Delete Group</a></p>";

                // Fetch and display the members of the group
                $membersStmt = $conn->prepare("SELECT u.User_Fname, u.User_Lname 
                                               FROM User u 
                                               JOIN User_Group ug ON u.User_ID = ug.User_ID 
                                               WHERE ug.Group_ID = ?");
                $membersStmt->execute([$group['Group_ID']]);
                $members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);

                echo "<p><strong>Members:</strong></p><ul>";
                foreach ($members as $member) {
                    echo "<li>" . htmlspecialchars($member['User_Fname']) . " " . htmlspecialchars($member['User_Lname']) . "</li>";
                }
                echo "</ul></div>";
            }
            ?>
            </div>
        </section>
    </div>
</body>
<footer class="footer">
        <p class="footer__text">
            <a href="#">About</a> | <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Use</a> | <a href="#">Contact Us</a>
        </p>
</footer>
</html>
