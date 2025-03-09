<?php
include 'db.php'; // Database connection

$sql = "SELECT * FROM jobs ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Listings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Available Jobs</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Salary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['company']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['salary']; ?></td>
                    <td>
                        <a href="job_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View</a>
                        <a href="apply.php?job_id=<?php echo $row['id']; ?>" class="btn btn-success">Apply</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
