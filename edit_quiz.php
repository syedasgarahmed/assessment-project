<?php
session_start();
require 'db.php'; // Include the database connection file

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Get the quiz ID from the URL
if (isset($_GET['id'])) {
    $quizId = $_GET['id'];
} else {
    echo "Quiz ID is not specified.";
    exit();
}

// Function to fetch questions for a specific quiz
function getQuestions($conn, $quizId) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle adding a new question
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_question'])) {
    $questionText = trim($_POST['question_text']);
    $correctAnswer = $_POST['correct_answer'];

    if (!empty($questionText) && in_array($correctAnswer, ['Yes', 'No'])) {
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, correct_answer) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $quizId, $questionText, $correctAnswer);
        
        if ($stmt->execute()) {
            echo "Question added successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please provide a valid question and answer.";
    }
}

// Handle deleting a question
if (isset($_GET['delete_question_id'])) {
    $questionId = $_GET['delete_question_id'];
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->bind_param("i", $questionId);
    
    if ($stmt->execute()) {
        echo "Question deleted successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch all questions for the quiz
$questions = getQuestions($conn, $quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quiz</title>
</head>
<body>
    <h1>Edit Quiz</h1>
    <h2>Add a New Question</h2>
    <form method="POST" action="">
        <textarea name="question_text" placeholder="Enter your question here" required></textarea><br><br>
        <label>
            <input type="radio" name="correct_answer" value="Yes" required> Yes
        </label>
        <label>
            <input type="radio" name="correct_answer" value="No" required> No
        </label><br><br>
        <button type="submit" name="add_question">Add Question</button>
    </form>

    <h2>Existing Questions</h2>
    <?php if (count($questions) > 0): ?>
        <ul>
            <?php foreach ($questions as $question): ?>
                <li>
                    <?php echo htmlspecialchars($question['question_text']); ?> (Correct Answer: <?php echo htmlspecialchars($question['correct_answer']); ?>)
                    <a href="edit_quiz.php?id=<?php echo $quizId; ?>&delete_question_id=<?php echo $question['id']; ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No questions found for this quiz.</p>
    <?php endif; ?>

    <a href="teacher.php">Back to Dashboard</a>
</body>
</html>
