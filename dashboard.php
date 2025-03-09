<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['user_role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['user_email']; ?>!</h2>
        <p>Role: <?php echo ucfirst($role); ?></p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>