<?php
session_start();
require 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to enroll.");
}

$user_id = $_SESSION['user_id'];

// Ensure course ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid course selection.");
}

$course_id = intval($_GET['id']); // Convert to integer for security

// Check if the user is already enrolled
$check_sql = "SELECT * FROM enrollments WHERE user_id = $user_id AND course_id = $course_id";
$check_result = $conn->query($check_sql);

if ($check_result->num_rows == 0) {
    // Enroll user
    $sql = "INSERT INTO enrollments (user_id, course_id) VALUES ($user_id, $course_id)";
    if ($conn->query($sql)) {
        echo "Enrolled successfully!";
    } else {
        echo "Error enrolling: " . $conn->error;
    }
} else {
    echo "You are already enrolled in this course.";
}

$conn->close();
?>
<a href="homepage.php">Back to homepage</a>