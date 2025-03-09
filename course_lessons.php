<?php
include 'db.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($course_id <= 0) {
    echo "<p>Invalid Course ID.</p>";
    exit;
}

// Handle lesson completion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_lesson'])) {
    $lesson_id = intval($_POST['lesson_id']);

    // Check if lesson is already completed
    $checkSQL = "SELECT 1 FROM lesson_progress WHERE user_id = ? AND lesson_id = ?";
    $stmt = $conn->prepare($checkSQL);
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    $progressExists = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if (!$progressExists) {
        // Insert lesson completion record
        $insertSQL = "INSERT INTO lesson_progress (user_id, lesson_id, course_id, completed_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertSQL);
        $stmt->bind_param("iii", $user_id, $lesson_id, $course_id);
        $stmt->execute();
        $stmt->close();

        // Calculate progress
        $totalLessonsSQL = "SELECT COUNT(*) as total FROM course_lessons WHERE course_id = ?";
        $stmt = $conn->prepare($totalLessonsSQL);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $totalLessons = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();

        $completedLessonsSQL = "SELECT COUNT(*) as completed FROM lesson_progress lp 
                                JOIN course_lessons cl ON lp.lesson_id = cl.lesson_id 
                                WHERE lp.user_id = ? AND cl.course_id = ?";
        $stmt = $conn->prepare($completedLessonsSQL);
        $stmt->bind_param("ii", $user_id, $course_id);
        $stmt->execute();
        $completedLessons = $stmt->get_result()->fetch_assoc()['completed'];
        $stmt->close();

        // Calculate progress percentage
        $progressPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;

        // Update progress in `enrollments` table
        $updateEnrollmentSQL = "UPDATE enrollments SET progress = ? WHERE user_id = ? AND course_id = ?";
        $stmt = $conn->prepare($updateEnrollmentSQL);
        $stmt->bind_param("dii", $progressPercentage, $user_id, $course_id);
        $stmt->execute();
        $stmt->close();
    }

    // Reload page to reflect progress
    header("Location: course_lessons.php?course_id=$course_id");
    exit;
}

// Fetch all lessons for the course
$sql = "SELECT * FROM course_lessons WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$lessons = $stmt->get_result();
$stmt->close();

$totalLessonsSQL = "SELECT COUNT(*) as total FROM course_lessons WHERE course_id = ?";
$stmt = $conn->prepare($totalLessonsSQL);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$totalLessons = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Check if all lessons are completed
$checkAllCompletedSQL = "SELECT COUNT(*) as completed FROM lesson_progress lp 
                         JOIN course_lessons cl ON lp.lesson_id = cl.lesson_id 
                         WHERE lp.user_id = ? AND cl.course_id = ?";
$stmt = $conn->prepare($checkAllCompletedSQL);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$completedLessons = $stmt->get_result()->fetch_assoc()['completed'];
$stmt->close();

$allLessonsCompleted = ($completedLessons == $totalLessons);
?>

<h2>Course Lessons</h2>

<?php while ($lesson = $lessons->fetch_assoc()) { 
    $lesson_id = $lesson['lesson_id'];

    // Check if the lesson is completed
    $checkProgressSQL = "SELECT 1 FROM lesson_progress WHERE user_id = ? AND lesson_id = ?";
    $stmt = $conn->prepare($checkProgressSQL);
    $stmt->bind_param("ii", $user_id, $lesson_id);
    $stmt->execute();
    $progressResult = $stmt->get_result();
    $isCompleted = ($progressResult->num_rows > 0);
    $stmt->close();
?>
    <div style="border: 1px solid black; padding: 10px; margin-bottom: 10px;">
        <h3><?php echo htmlspecialchars($lesson['lesson_title']); ?></h3>
        <p><?php echo htmlspecialchars($lesson['lesson_description']); ?></p>

        <!-- Mark as Complete Form -->
        <form method="POST" action="">
            <input type="hidden" name="lesson_id" value="<?php echo htmlspecialchars($lesson_id); ?>">
            <button type="submit" name="complete_lesson" <?php echo $isCompleted ? 'disabled' : ''; ?>>
                <?php echo $isCompleted ? 'Completed' : 'Mark as Complete'; ?>
            </button>
        </form>
    </div>
<?php } ?>

<!-- Display Progress -->
<?php
// Fetch user progress from enrollments
$userProgressSQL = "SELECT progress FROM enrollments WHERE user_id = ? AND course_id = ?";
$stmt = $conn->prepare($userProgressSQL);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$userProgress = $stmt->get_result()->fetch_assoc()['progress'];
$stmt->close();
?>

<h2>Course Progress</h2>
<?php if ($userProgress == 100) { ?>
    <h3>Congratulations! You have completed all lessons.</h3>
    <a href="course_quiz.php?course_id=<?= $course_id ?>">
        <button style="padding: 10px 20px; background-color: blue; color: white; border: none; cursor: pointer;">
            Take the Quiz
        </button>
    </a>
<?php } else { ?>
    <p>Your progress: <?= round($userProgress, 2) ?>%</p>
<?php } ?>
