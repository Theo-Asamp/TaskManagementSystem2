<?php
session_start();
include 'db.php'; // Include the database connection file

$userId = $_SESSION['user_id'] ?? 1;

// Check if the user is logged in (check if first name and last name are set in the session)
if (!isset($_SESSION['User_Fname'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user details from the database based on first and last name from session
$first_name = $_SESSION['User_Fname'];
$last_name = $_SESSION['User_Lname'];

$sql = "SELECT User_ID, User_Fname, User_Lname, User_Email, User_TelNo, User_Role FROM User WHERE User_Fname = :first_name AND User_Lname = :last_name";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':first_name', $first_name);
$stmt->bindParam(':last_name', $last_name);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists, if not redirect to login


// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Update user details in the database
    $update_sql = "UPDATE User SET User_Fname = :first_name, User_Lname = :last_name, User_Email = :email, User_TelNo = :phone WHERE User_Fname = :session_first_name AND User_Lname = :session_last_name";
    $stmt = $conn->prepare($update_sql);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':session_first_name', $_SESSION['user_fname']);
    $stmt->bindParam(':session_last_name', $_SESSION['user_lname']);

    if ($stmt->execute()) {
        // Update session variables to reflect the changes
        $_SESSION['user_fname'] = $first_name; // Update session with new first name
        $_SESSION['user_lname'] = $last_name;  // Update session with new last name
        $_SESSION['user_email'] = $email;      // Update session with new email
        $_SESSION['user_phone'] = $phone;      // Update session with new phone number

        // If successful, refresh the page with updated information
        header("Location: profile.php");
        exit();
    } else {
        $error = "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="css/global.css">
</head>
<body>
<header class="navbar">
    <a href="dashboard.php" class="navbar_title2"><h1>taskly</h1></a>
    <a href="logout.php" class="navbar-index">Log out</a> 
</header>
<div class="container">
    <nav class="sidebar">
        <div class="user-profile">
            <a href="profile.php"><img src="images/Sample_User_Icon.png" alt="User"></a>
            <h4>
                <?php echo htmlspecialchars($user['User_Fname']); ?>
            </h4> 
        </div>
        <ul>
            <li><a href="tasks.php"><i class="sidebar-texts"></i> <span>Tasks</span></a></li> 
            <li><a href="groups.php"><i class="sidebar-text"></i> <span>Groups</span></a></li>
        </ul>
    </nav>

    <section class="main-content">
    <div class="profile-page_content">
        <h2>Profile Information</h2>
        <div class="profile-page_section">

        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <form action="profile.php" method="POST" class="profile-page_form">
            <label for="first_name" class="profile-page__label">First Name:</label>
            <input type="text" class="profile-page__input" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['User_Fname']); ?>" required>

            <label for="last_name" class="profile-page__label">Last Name:</label>
            <input type="text" class="profile-page__input" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['User_Lname']); ?>" required>

            <label for="email" class="profile-page__label">Email:</label>
            <input type="email" class="profile-page__input" id="email" name="email" value="<?= htmlspecialchars($user['User_Email']) ?>" required>

            <label for="phone" class="profile-page__label">Phone:</label>
            <input type="text" class="profile-page__input" id="phone" name="phone" value="<?= htmlspecialchars($user['User_TelNo']) ?>">

            <button type="submit" class="btn btn--update">Update Profile</button>
        </form>
        </div>
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
