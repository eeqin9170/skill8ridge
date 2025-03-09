<?php
require 'db.php';

$user_id = $_GET['user_id'];
$course_id = $_GET['course_id'];

$stmt = $conn->prepare("SELECT progress FROM enrollments WHERE user_id = ? AND course_id = ?");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['progress_percentage' => $row['progress']]);
} else {
    echo json_encode(['progress_percentage' => 0]);
}
?>
