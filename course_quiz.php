<?php
include 'db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied! Please log in.");
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id <= 0) {
    die("Invalid Course ID.");
}

// Fetch quiz questions
$quizSQL = "SELECT * FROM quizzes WHERE course_id = ?";
$stmt = $conn->prepare($quizSQL);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$quizQuestions = $stmt->get_result();
$stmt->close();

// Check if there are any questions
if ($quizQuestions->num_rows === 0) {
    die("<p>No quiz available for this course.</p>");
}

?>

<h2>Course Quiz</h2>
<form action="submit_quiz.php" method="POST">
    <input type="hidden" name="course_id" value="<?= $course_id ?>">
    <?php while ($row = $quizQuestions->fetch_assoc()) { ?>
        <p><strong><?= htmlspecialchars($row['question']) ?></strong></p>
        <label><input type="radio" name="answers[<?= $row['id'] ?>]" value="A" required> <?= htmlspecialchars($row['option_a']) ?></label><br>
        <label><input type="radio" name="answers[<?= $row['id'] ?>]" value="B"> <?= htmlspecialchars($row['option_b']) ?></label><br>
        <label><input type="radio" name="answers[<?= $row['id'] ?>]" value="C"> <?= htmlspecialchars($row['option_c']) ?></label><br>
        <label><input type="radio" name="answers[<?= $row['id'] ?>]" value="D"> <?= htmlspecialchars($row['option_d']) ?></label><br>
        <br>
    <?php } ?>
    <button type="submit">Submit Quiz</button>
</form>

<a href="course_lessons.php?course_id=<?= $course_id ?>">Back to Course</a>
