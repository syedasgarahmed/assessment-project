<?php
session_start();
require 'db.php'; // Include the database connection file

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Fetch all quizzes
function getQuizzes($conn) {
    $stmt = $conn->prepare("SELECT * FROM quizzes");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch results for a specific quiz
function getResults($conn, $quizId) {
    $stmt = $conn->prepare("
        SELECT users.username, results.score, quizzes.title 
        FROM results 
        JOIN users ON results.student_id = users.id 
        JOIN quizzes ON results.quiz_id = quizzes.id 
        WHERE results.quiz_id = ?
    ");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch quiz analytics (e.g., average score)
function getQuizAnalytics($conn, $quizId) {
    $stmt = $conn->prepare("SELECT AVG(score) AS average_score FROM results WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result;
}

// Handle quiz selection
$selectedQuizId = null;
$results = [];
$quizAnalytics = [];

if (isset($_GET['quiz_id'])) {
    $selectedQuizId = $_GET['quiz_id'];
    $results = getResults($conn, $selectedQuizId);
    $quizAnalytics = getQuizAnalytics($conn, $selectedQuizId);
}

// Fetch all quizzes for display
$quizzes = getQuizzes($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Quiz Results</title>
</head>
<body>
    <h1>View Quiz Results</h1>

    <h2>Select a Quiz</h2>
    <form method="GET" action="">
        <select name="quiz_id" required>
            <option value="">Select a quiz</option>
            <?php foreach ($quizzes as $quiz): ?>
                <option value="<?php echo $quiz['id']; ?>" <?php echo ($quiz['id'] == $selectedQuizId) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($quiz['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Results</button>
    </form>

    <?php if ($selectedQuizId && count($results) > 0): ?>
        <h2>Results for Quiz: <?php echo htmlspecialchars($results[0]['title']); ?></h2>
        <table border="1">
            <tr>
                <th>Student</th>
                <th>Score</th>
            </tr>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['username']); ?></td>
                    <td><?php echo htmlspecialchars($result['score']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Analytics</h3>
        <p>Average Score: <?php echo number_format($quizAnalytics['average_score'], 2); ?></p>
    <?php elseif ($selectedQuizId): ?>
        <p>No results found for this quiz.</p>
    <?php endif; ?>

    <a href="teacher.php">Back to Dashboard</a>
</body>
</html>
