<?php
session_start();
include 'db.php'; // Include your database connection file

if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    // Fetch the quiz details
    $query = "SELECT * FROM quizzes WHERE quiz_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz_result = $stmt->get_result();
    $quiz = $quiz_result->fetch_assoc();

    // Fetch the questions for the quiz
    $query = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();


    if (isset($_POST['submit'])) {
        $quiz_id = $_GET['quiz_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
    
        // Update quiz details
        $query = "UPDATE quizzes SET title = ?, description = ? WHERE quiz_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $title, $description, $quiz_id);
        $stmt->execute();
    
        // Update questions and answers
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') !== false) {
                $question_id = str_replace('question_', '', $key);
                $question_text = $_POST["question_$question_id"];
                $option_a = $_POST["option_a_$question_id"];
                $option_b = $_POST["option_b_$question_id"];
                $option_c = $_POST["option_c_$question_id"];
                $option_d = $_POST["option_d_$question_id"];
                $correct_answer = $_POST["correct_answer_$question_id"];
    
                $query = "UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE question_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);
                $stmt->execute();
            }
        }
    
        echo "Quiz updated successfully!";
    
    }}
?>

<h2>Edit Quiz</h2>
<form method="POST" action="edit_quiz.php?quiz_id=<?php echo $quiz_id; ?>">
    <label for="title">Quiz Title:</label>
    <input type="text" name="title" value="<?php echo $quiz['title']; ?>" required>

    <label for="description">Description:</label>
    <textarea name="description" required><?php echo $quiz['description']; ?></textarea>

    <h3>Questions</h3>
    <?php while ($question = $questions_result->fetch_assoc()) { ?>
        <div class="question">
            <label>Question <?php echo $question['question']; ?>:</label>
            <textarea name="question_<?php echo $question['question_id']; ?>" required><?php echo $question['question_text']; ?></textarea>

            <label for="option_a">Option A:</label>
            <input type="text" name="option_a_<?php echo $question['question_id']; ?>" value="<?php echo $question['option_a']; ?>" required>

            <label for="option_b">Option B:</label>
            <input type="text" name="option_b_<?php echo $question['question_id']; ?>" value="<?php echo $question['option_b']; ?>" required>

            <label for="option_c">Option C:</label>
            <input type="text" name="option_c_<?php echo $question['question_id']; ?>" value="<?php echo $question['option_c']; ?>" required>

            <label for="option_d">Option D:</label>
            <input type="text" name="option_d_<?php echo $question['question_id']; ?>" value="<?php echo $question['option_d']; ?>" required>

            <label for="correct_answer">Correct Answer:</label>
            <select name="correct_answer_<?php echo $question['question_id']; ?>" required>
                <option value="a" <?php echo $question['correct_answer'] == 'a' ? 'selected' : ''; ?>>Option A</option>
                <option value="b" <?php echo $question['correct_answer'] == 'b' ? 'selected' : ''; ?>>Option B</option>
                <option value="c" <?php echo $question['correct_answer'] == 'c' ? 'selected' : ''; ?>>Option C</option>
                <option value="d" <?php echo $question['correct_answer'] == 'd' ? 'selected' : ''; ?>>Option D</option>
            </select>
        </div>
    <?php } ?>
    <button type="submit" name="submit">Update Quiz</button>
</form>
