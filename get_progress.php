<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$sql = "SELECT completed_percentage FROM user_progress WHERE user_id = $user_id AND course_id = $course_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["progress" => $row["completed_percentage"]]);
} else {
    echo json_encode(["progress" => 0]);
}

$conn->close();
?>
