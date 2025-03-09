<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please log in.");
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($course_id <= 0 || !isset($_POST['answers'])) {
    die("Invalid quiz submission.");
}

$answers = $_POST['answers']; // User-submitted answers
$totalQuestions = count($answers);
$correctCount = 0;

// Fetch correct answers from the database
$quizSQL = "SELECT id, correct_answer FROM quizzes WHERE course_id = ?";
$stmt = $conn->prepare($quizSQL);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$correctAnswers = [];
while ($row = $result->fetch_assoc()) {
    $correctAnswers[$row['id']] = $row['correct_answer'];
}
$stmt->close();

// Compare user answers with correct answers
foreach ($answers as $question_id => $userAnswer) {
    if (isset($correctAnswers[$question_id]) && $correctAnswers[$question_id] === $userAnswer) {
        $correctCount++;
    }
}

// Calculate quiz score
$score = ($totalQuestions > 0) ? round(($correctCount / $totalQuestions) * 100) : 0;

// Store the quiz result in the database
$insertSQL = "INSERT INTO quiz_results (user_id, course_id, score) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertSQL);
$stmt->bind_param("iii", $user_id, $course_id, $score);
$stmt->execute();
$stmt->close();

echo "<h2>Quiz Submitted</h2>";
echo "<p>Your Score: $score%</p>";
echo "<a href='course_lessons.php?course_id=$course_id'>Back to Course</a>";
?>
