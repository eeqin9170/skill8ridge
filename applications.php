<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

$sql = "SELECT job_applications.*, jobs.title, jobs.company FROM job_applications 
        JOIN jobs ON job_applications.job_id = jobs.id 
        WHERE job_applications.user_id = $user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
</head>
<body>
    <h2>My Job Applications</h2>
    <table border="1">
        <tr>
            <th>Job Title</th>
            <th>Company</th>
            <th>Status</th>
            <th>Applied On</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['company']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td><?php echo $row['applied_at']; ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
