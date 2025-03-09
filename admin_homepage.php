<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch users categorized by role
$sqlUsers = "SELECT id, username, email, role FROM users WHERE role IN ('user', 'employer')";
$resultUsers = $conn->query($sqlUsers);

// Fetch courses
$sqlCourses = "SELECT id, title, description FROM courses";
$resultCourses = $conn->query($sqlCourses);

$sql = "SELECT * FROM jobs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Welcome, Admin!</h2>

        <!-- User List -->
        <h3>Users & Employers</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultUsers->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Course List -->
        <h3>Available Courses</h3>
        <a href="create_course.php" class="btn btn-primary mb-3">Create Course</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $resultCourses->fetch_assoc()): ?>
                <tr>
                    <td><?= $course['id'] ?></td>
                    <td><?= htmlspecialchars($course['title']) ?></td>
                    <td><?= htmlspecialchars($course['description']) ?></td>
                    <td>
                        <a href="edit_course.php?id=<?= $course['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="edit_course_resources.php?id=<?= $course['id'] ?>" class="btn btn-info btn-sm">Edit Resources</a>
                        <a href="edit_quiz.php?id=<?= $course['id'] ?>" class="btn btn-info btn-sm">Edit Quiz</a>
                        <a href="delete_course.php?id=<?= $course['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h2>Job Listings</h2>
    <a href="create_job.php">Add New Job</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Company</th>
            <th>Location</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['company'] ?></td>
                <td><?= $row['location'] ?></td>
                <td>
                    <a href="edit_job.php?id=<?= $row['id'] ?>">Edit</a> |
                    <a href="delete_job.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    </div>

</body>
</html>
