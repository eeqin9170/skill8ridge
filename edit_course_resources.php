<?php
session_start();
include 'db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to edit course resources.";
    exit;
}

// Get course ID from URL
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$course_id = $_GET['id'];

// Fetch current course resources
$sql = "SELECT * FROM course_resources WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission for adding/editing resources
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resource_name = trim($_POST['resource_name']);
    $resource_link = trim($_POST['resource_link']);

    if (!empty($resource_name) && !empty($resource_link)) {
        // Insert new resource
        $sql = "INSERT INTO course_resources (course_id, resource_name, resource_link, uploaded_at) 
                VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $course_id, $resource_name, $resource_link);

        if ($stmt->execute()) {
            echo "Resource added successfully!";
        } else {
            echo "Error adding resource.";
        }
        $stmt->close();
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course Resources</title>
</head>
<body>
    <h2>Edit Course Resources</h2>

    <!-- Add New Resource -->
    <form method="POST">
        <label>Resource Name:</label>
        <input type="text" name="resource_name" required><br>

        <label>Resource Link:</label>
        <input type="text" name="resource_link" required><br>

        <button type="submit">Add Resource</button>
    </form>

    <hr>

    <!-- Display Existing Resources -->
    <h3>Existing Resources</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <a href="<?= htmlspecialchars($row['resource_link']) ?>" target="_blank">
                    <?= htmlspecialchars($row['resource_name']) ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
