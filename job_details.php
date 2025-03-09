<?php
include 'db.php';
$job_id = $_GET['id'];

$sql = "SELECT * FROM jobs WHERE id = $job_id";
$result = $conn->query($sql);
$job = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Details</title>
</head>
<body>
    <h2><?php echo $job['title']; ?></h2>
    <p><strong>Company:</strong> <?php echo $job['company']; ?></p>
    <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
    <p><strong>Salary:</strong> <?php echo $job['salary']; ?></p>
    <p><strong>Description:</strong> <?php echo $job['description']; ?></p>
    <a href="apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-success">Apply Now</a>
</body>
</html>
