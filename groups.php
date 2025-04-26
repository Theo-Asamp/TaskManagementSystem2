<?php
session_start();
include('db.php');

// Check if logged in
$userId = $_SESSION['User_ID'] ?? null;
if (!$userId) {
    die("You must be logged in.");
}

// Get all available groups (for join dropdown)
$allGroups = $conn->query("SELECT Group_ID, Group_Name FROM GroupTable")->fetchAll(PDO::FETCH_ASSOC);

// Get groups user is already in
$groupStmt = $conn->prepare("
    SELECT g.Group_ID, g.Group_Name 
    FROM GroupTable g
    JOIN User_Group ug ON g.Group_ID = ug.Group_ID
    WHERE ug.User_ID = ?
");
$groupStmt->execute([$userId]);
$userGroups = $groupStmt->fetchAll(PDO::FETCH_ASSOC);

// Get tasks for groups user joined
$tasksByGroup = [];
if (!empty($userGroups)) {
    foreach ($userGroups as $group) {
        $taskStmt = $conn->prepare("SELECT * FROM Group_Task WHERE Group_ID = ?");
        $taskStmt->execute([$group['Group_ID']]);
        $tasks = $taskStmt->fetchAll(PDO::FETCH_ASSOC);

        $tasksByGroup[$group['Group_ID']] = [
            'name' => $group['Group_Name'],
            'tasks' => $tasks
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Groups</title>
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
            <h4><?php echo htmlspecialchars($_SESSION['User_Fname']); ?></h4>
        </div>
        <ul>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="groups.php">Groups</a></li>
        </ul>
    </nav>

    <section class="main-content">
        <div class="main-content-container">
            <h2>Join a Group</h2>

            <!-- Join Group Form -->
            <form method="POST" action="join-group.php">
                <label for="group_id">Select a Group:</label>
                <select name="group_id" id="group_id" required>
                    <option value="" disabled selected>-- Choose a group --</option>
                    <?php foreach ($allGroups as $group): ?>
                        <?php
                        $alreadyJoined = false;
                        foreach ($userGroups as $joined) {
                            if ($joined['Group_ID'] == $group['Group_ID']) {
                                $alreadyJoined = true;
                                break;
                            }
                        }
                        if (!$alreadyJoined):
                        ?>
                            <option value="<?= $group['Group_ID'] ?>"><?= htmlspecialchars($group['Group_Name']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="join_group">Join</button>
            </form>

            <hr>

            <h2>Your Group Tasks</h2>

            <?php if (empty($tasksByGroup)): ?>
                <p>You haven't joined any groups yet. Join one to see tasks.</p>
            <?php else: ?>
                <?php foreach ($tasksByGroup as $groupId => $groupData): ?>
                    <div class="group-panel">
                        <h3><?= htmlspecialchars($groupData['name']) ?></h3>

                        <?php if (empty($groupData['tasks'])): ?>
                            <p>No tasks in this group.</p>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($groupData['tasks'] as $task): ?>
                                    <li>
                                        <h4><?= htmlspecialchars($task['GroupTask_Name']) ?></h4>
                                        <p><?= htmlspecialchars($task['GroupTask_Description'] ?? 'No description.') ?></p>
                                        <p><strong>Status:</strong> <?= htmlspecialchars($task['GroupTask_Status']) ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <form action="leave-group.php" method="POST" style="margin-top:10px;">
                            <input type="hidden" name="group_id" value="<?= $groupId ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to leave this group?')">Leave Group</button>
                        </form>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
