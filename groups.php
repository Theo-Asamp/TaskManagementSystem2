<?php
session_start();
include('db.php');

$userId = $_SESSION['user_id'] ?? null;
$groupId = $_GET['group_id'] ?? null;



// Get group info
$group = $conn->prepare("SELECT Group_Name FROM GroupTable WHERE Group_ID = ?");
$group->execute([$groupId]);
$groupName = $group->fetchColumn();

// Get tasks in this group
$stmt = $conn->prepare("SELECT * FROM Group_Task WHERE GroupTask_ID = ?");
$stmt->execute([$groupId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($groupName) ?> - Group Tasks</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
    <header class="navbar">
        <a href="dashboard.php" class="navbar_title2"><h1>taskly</h1></a>
        <a class="navbar-index" href="logout.php">Log out</a>
    </header>

    <div class="container">
        <nav class="sidebar">
            <div class="user-profile">
                <a href="profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
                <h4><?php echo $_SESSION['User_Fname']; ?></h4>
            </div>
            <ul>
                <li><a href="tasks.php">Tasks</a></li>
                <li><a href="groups.php">Groups</a></li>
            </ul>
        </nav>

        <section class="main-content">
            <div class="home-box">
                <h2>Tasks for <?= htmlspecialchars($groupName) ?></h2>

                <?php if (!$tasks): ?>
                    <p>No tasks in this group.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($tasks as $task): ?>
                            <li>
                                <h4><?= htmlspecialchars($task['Task_Title']) ?></h4>
                                <p><?= htmlspecialchars($task['Task_Description']) ?></p>
                                <p><strong>Deadline:</strong> <?= htmlspecialchars($task['Task_Deadline']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
                <h3>Join a Group</h3>
                <form method="POST" action="join-group.php">
                <select name="group_id">
                 <?php
                $groups = $conn->query("SELECT * FROM GroupTable")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($groups as $group) {
                    echo "<option value='{$group['Group_ID']}'>{$group['Group_Name']}</option>";
                }
                ?>
                </select>
                <button type="submit" name="join_group">Join Group</button>
                </form>

        </section>
    </div>

    <footer class="footer">
        <p class="footer__text">
            <a href="#">About</a> | <a href="#">Privacy Policy</a> |
            <a href="#">Terms of Use</a> | <a href="#">Contact Us</a>
        </p>
    </footer>
</body>
</html>
