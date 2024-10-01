<?php
session_start();
require_once '../includes/db.php'; // Database connection
require_once '../includes/header.php'; // Assuming the header is included here

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit; // Stop the execution if session is not set
}

// Fetch the lecturer details from the users table
$lecturer_id = $_SESSION['user_id']; // Lecturer's user ID from the session
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id AND role = 'lecturer'");
$stmt->bindParam(':user_id', $lecturer_id, PDO::PARAM_INT);
$stmt->execute();
$lecturer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lecturer) {
    // Handle the case where the lecturer was not found or role mismatch
    echo "No lecturer found with this ID or role mismatch.";
    exit; 
}

// Fetch courses assigned to the lecturer
$stmt_courses = $conn->prepare("SELECT * FROM courses WHERE lecturer_id = :lecturer_id");
$stmt_courses->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
$stmt_courses->execute();
$courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
$course_count = count($courses); // Count the number of courses
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Lecturer Profile</h3>
        </div>
        <div class="card-body">
            <h5 class="card-title">Lecturer Information</h5>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($lecturer['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($lecturer['email']); ?></p>
            <p><strong>Total Courses Assigned:</strong> <?php echo $course_count; ?></p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5>Courses Taught</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php if ($courses): ?>
                    <?php foreach ($courses as $course): ?>
                        <li class="list-group-item">
                            <?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['course_name']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No courses assigned.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

</body>
</html>
