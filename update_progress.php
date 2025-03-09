<?php
include 'db.php';
header('Content-Type: application/json');
session_start();

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['lesson_id'], $data['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

$user_id = intval($data['user_id']);
$lesson_id = intval($data['lesson_id']);

// Ensure session user ID matches the request
if ($user_id !== $_SESSION['user_id']) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}

// Check if the lesson is already completed
$checkSQL = "SELECT 1 FROM lesson_progress WHERE user_id = ? AND lesson_id = ?";
$stmt = $conn->prepare($checkSQL);
$stmt->bind_param("ii", $user_id, $lesson_id);
$stmt->execute();
$progressExists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($progressExists) {
    echo json_encode(["status" => "error", "message" => "Lesson already completed"]);
    exit;
}

// Insert completion record
$insertSQL = "INSERT INTO lesson_progress (user_id, lesson_id, completed_at
