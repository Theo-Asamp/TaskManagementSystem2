<?php
session_start();
include('db.php');  // Database connection

// Ensure the user is logged in and session variables are set
if (!isset($_SESSION['User_ID'])) {
    // Redirect to login page if the session is not set
    header("Location: login.php");
    exit();
}

// Handling the group creation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_group'])) {
    $group_name = $_POST['group_name'];

    // Insert the group into the database using PDO
    $query = "INSERT INTO GroupTable (Group_Name, User_ID) VALUES (:group_name, :user_id)";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':group_name', $group_name);
    $stmt->bindParam(':user_id', $_SESSION['User_ID'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Group created successfully!";
    } else {
        echo "Error creating group.";
    }
}

// Handling the invitation form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invite_user'])) {
    $user_id = $_POST['user_id'];  // User to invite
    $group_id = $_POST['group_id'];  // Group to invite the user to

    // Insert into User_Group table using PDO
    $query = "INSERT INTO User_Group (User_ID, Group_ID) VALUES (:user_id, :group_id)";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "User has been invited to the group!";
    } else {
        echo "Error inviting user.";
    }
}

// Handling the task addition form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $group_id = $_POST['group_id'];  

    // Insert the task into the database using PDO
    $query = "INSERT INTO Group_Task (GroupTask_Name, GroupTask_Description, GroupTask_Status, Group_ID) 
              VALUES (:task_title, :task_description, 'Pending', :group_id)";
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':task_title', $task_title);
    $stmt->bindParam(':task_description', $task_description);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Task added successfully!";
    } else {
        echo "Error adding task.";
    }
}

// Handle group deletion request
if (isset($_GET['delete_group_id'])) {
    $group_id = $_GET['delete_group_id'];

    // Fetch the group information to verify ownership
    $query = "SELECT * FROM GroupTable WHERE Group_ID = :group_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->execute();
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the group exists and if the logged-in user is the owner
    if ($group && ($_SESSION['User_ID'] == $group['User_ID'] || $_SESSION['User_Role'] == 'admin')) {
        // Begin transaction to ensure both operations (deleting tasks and group) are completed successfully
        $conn->beginTransaction();

        try {
            // Step 1: Delete all tasks associated with the group
            $delete_tasks_query = "DELETE FROM Group_Task WHERE Group_ID = :group_id";
            $delete_tasks_stmt = $conn->prepare($delete_tasks_query);
            $delete_tasks_stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $delete_tasks_stmt->execute();

            // Step 2: Delete the group
            $delete_group_query = "DELETE FROM GroupTable WHERE Group_ID = :group_id";
            $delete_group_stmt = $conn->prepare($delete_group_query);
            $delete_group_stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $delete_group_stmt->execute();

            // Commit the transaction
            $conn->commit();

            // Redirect back to the group management page after deletion
            echo "Group and all associated tasks deleted successfully!";
            header("Location: admin-group.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction in case of an error
            $conn->rollBack();
            echo "Error deleting group and tasks: " . $e->getMessage();
        }
    } else {
        echo "You don't have permission to delete this group.";
    }
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
                <p>Admin</p>
            </div>
            <ul>
                <li><a href="admin-tasks.php"><i class="sidebar-texts"></i> <span>Manage Tasks</span></a></li> 
                <li><a href="admin-group.php"><i class="sidebar-text"></i> <span>Manage Groups</span></a></li>
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

                <!-- Groups Overview Section -->
                <h2>Groups Overview</h2>
                <?php
                // Fetch available groups created by the admin using PDO
                $query = "SELECT * FROM GroupTable WHERE User_ID = :user_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':user_id', $_SESSION['User_ID'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($result) > 0) {
                    foreach ($result as $group) {
                        echo "<p>Group Name: " . $group['Group_Name'] . " ";
                        echo "<a href='admin-group.php?delete_group_id=" . $group['Group_ID'] . "'>Delete Group</a></p>";
                    }
                } else {
                    echo "<p>No groups available.</p>";
                }
                ?>

                <hr>

                <!-- Invite Users to Group Form -->
                <h2>Invite Users to Group</h2>
                <form action="admin-group.php" method="POST">
                    <label for="group_id">Select Group:</label>
                    <select name="group_id" id="group_id">
                        <?php
                        // Fetch available groups for the admin using PDO
                        $query = "SELECT * FROM GroupTable WHERE User_ID = :user_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':user_id', $_SESSION['User_ID'], PDO::PARAM_INT);
                        $stmt->execute();
                        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($groups as $group) {
                            echo "<option value='" . $group['Group_ID'] . "'>" . $group['Group_Name'] . "</option>";
                        }
                        ?>
                    </select>

                    <label for="user_id">Select User to Invite:</label>
                    <select name="user_id" id="user_id">
                        <?php
                        // Fetch users to invite using PDO
                        $query = "SELECT * FROM User";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($users as $user) {
                            echo "<option value='" . $user['User_ID'] . "'>" . $user['User_Fname'] . " " . $user['User_Lname'] . "</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" name="invite_user">Send Invite</button>
                </form>

                <hr>

                <!-- Add Task to Group Form -->
                <h2>Add Task to Group</h2>
                <form action="admin-group.php" method="POST">
                    <label for="group_id">Select Group:</label>
                    <select name="group_id" id="group_id">
                        <?php
                        // Fetch available groups for the admin using PDO
                        $query = "SELECT * FROM GroupTable WHERE User_ID = :user_id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':user_id', $_SESSION['User_ID'], PDO::PARAM_INT);
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
            </div>
        </section>
    </div>
</body>
<footer class="footer">
    <!-- Footer Content -->
</footer>
</html>
