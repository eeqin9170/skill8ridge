<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = 1; // Replace with actual user session ID
    $course_id = $_POST['course_id'];
    $lesson_id = $_POST['lesson_id'];

    // Mark lesson as completed
    $sql = "INSERT INTO lesson_progress (user_id, course_id, lesson_id, completed) 
            VALUES ($user_id, $course_id, $lesson_id, TRUE) 
            ON DUPLICATE KEY UPDATE completed = TRUE";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect back to course page after marking as complete
        header("Location: course_lessons.php?course_id=$course_id");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>
