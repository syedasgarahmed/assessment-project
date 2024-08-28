<?php
session_start();
require 'db.php'; // Include the database connection file

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];

// Fetch all quizzes
function getQuizzes($conn) {
    $stmt = $conn->prepare("SELECT * FROM quizzes");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all questions for a specific quiz
function getQuestions($conn, $quizId) {
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle quiz submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quiz_id'])) {
    $quizId = $_POST['quiz_id'];
    $questions = getQuestions($conn, $quizId);
    $score = 0;

    // Calculate the score based on the student's answers
    foreach ($questions as $question) {
        $questionId = $question['id'];
        if (isset($_POST['answer_' . $questionId]) && $_POST['answer_' . $questionId] === $question['correct_answer']) {
            $score++;
        }
    }

    // Save the result to the database
    $stmt = $conn->prepare("INSERT INTO results (student_id, quiz_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $studentId, $quizId, $score);
    
    if ($stmt->execute()) {
        echo "Quiz submitted successfully. Your score: $score/" . count($questions);
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch all quizzes for display
$quizzes = getQuizzes($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Welcome, Student</h1>

    <h2>Available Quizzes</h2>
    <?php if (count($quizzes) > 0): ?>
        <ul>
            <?php foreach ($quizzes as $quiz): ?>
                <li>
                    <?php echo htmlspecialchars($quiz['title']); ?>
                    <form method="POST" action="">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                        <button type="submit" name="start_quiz">Take Quiz</button>
                        <a href="view_results_s.php?quiz_id=<?php echo $quiz['id']; ?>">View Results</a>

                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No quizzes available at the moment.</p>
    <?php endif; ?>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_quiz'])): ?>
        <?php $quizId = $_POST['quiz_id']; ?>
        <?php $questions = getQuestions($conn, $quizId); ?>

        <h2>Take Quiz: <?php echo htmlspecialchars($quizzes[array_search($quizId, array_column($quizzes, 'id'))]['title']); ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="quiz_id" value="<?php echo $quizId; ?>">
            <?php foreach ($questions as $question): ?>
                <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                <label>
                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="Yes" required> Yes
                </label>
                <label>
                    <input type="radio" name="answer_<?php echo $question['id']; ?>" value="No" required> No
                </label>
            <?php endforeach; ?>
            <br><br>
            <button type="submit">Submit Quiz</button>
        </form>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
