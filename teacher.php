<?php
session_start();
require 'db.php'; // Include database connection file

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

// Fetch quizzes created by the teacher
function getQuizzes($conn, $teacherId) {
    $stmt = $conn->prepare("SELECT * FROM quizzes WHERE created_by = ?");
    $stmt->bind_param("i", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Add a new quiz
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_quiz'])) {
    $title = $_POST['title'];
    $teacherId = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, created_by) VALUES (?, ?)");
    $stmt->bind_param("si", $title, $teacherId);

    if ($stmt->execute()) {
        echo "Quiz added successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch quiz details
$teacherId = $_SESSION['user_id'];
$quizzes = getQuizzes($conn, $teacherId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
</head>
<body>
    <h1>Welcome, Teacher</h1>

    <h2>Add Quiz</h2>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Quiz Title" required>
        <button type="submit" name="add_quiz">Add Quiz</button>
    </form>

    <h2>Your Quizzes</h2>
    <?php if (count($quizzes) > 0): ?>
        <ul>
            <?php foreach ($quizzes as $quiz): ?>
                <li><?php echo htmlspecialchars($quiz['title']); ?> - 
                    <a href="edit_quiz.php?id=<?php echo $quiz['id']; ?>">Edit</a>
                    <a href="view_results_t.php?quiz_id=<?php echo $quiz['id']; ?>">View Results</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No quizzes found.</p>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>
