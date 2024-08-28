<?php
session_start();
require 'db.php'; // Include the database connection file

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

$studentId = $_SESSION['user_id'];

// Fetch results for the logged-in student
function getResults($conn, $studentId) {
    $stmt = $conn->prepare("
        SELECT r.id, q.title, r.score, COUNT(ques.id) as total_questions 
        FROM results r 
        JOIN quizzes q ON r.quiz_id = q.id 
        JOIN questions ques ON ques.quiz_id = q.id
        WHERE r.student_id = ? 
        GROUP BY r.id
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all results for the student
$results = getResults($conn, $studentId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Results</title>
</head>
<body>
    <h1>Your Quiz Results</h1>

    <?php if (count($results) > 0): ?>
        <table border="1">
            <tr>
                <th>Quiz Title</th>
                <th>Score</th>
                <th>Total Questions</th>
            </tr>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?php echo htmlspecialchars($result['title']); ?></td>
                    <td><?php echo htmlspecialchars($result['score']); ?></td>
                    <td><?php echo htmlspecialchars($result['total_questions']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You haven't taken any quizzes yet.</p>
    <?php endif; ?>

    <a href="student.php">Back to Dashboard</a>
    <a href="logout.php">Logout</a>
</body>
</html>
