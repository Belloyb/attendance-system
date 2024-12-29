<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';
include_once 'header.php';

// Check if lecturer is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    die("Access denied. Lecturer not logged in.");
}

$lecturer_id = $_SESSION['user_id']; // Assuming lecturer ID is stored as user_id in the session

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the total number of courses assigned
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_courses FROM courses WHERE lecturer_id = :lecturer_id");
    $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
    $stmt->execute();
    $total_courses = $stmt->fetchColumn();

    // Fetch the total number of students across all assigned courses
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT sc.student_id) AS total_students
        FROM student_courses sc
        JOIN courses c ON sc.course_id = c.course_id
        WHERE c.lecturer_id = :lecturer_id
    ");
    $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
    $stmt->execute();
    $total_students = $stmt->fetchColumn();

    // Fetch courses and student counts
    $stmt = $conn->prepare("
        SELECT c.course_code, c.course_name, 
               (SELECT COUNT(*) FROM student_courses WHERE course_id = c.course_id) AS student_count
        FROM courses c
        WHERE c.lecturer_id = :lecturer_id
    ");
    $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
        }
        .card h5 {
            font-weight: bold;
        }
        .card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: rgb(247, 247, 247);
        }
        .btn {
            font-size: 1.2rem;
            padding: 10px 20px;
        }
        h1 {
            font-weight: bold;
            color: #343a40;
        }
        .text-center a {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Lecturer Dashboard</h1>
    <div class="row text-center mt-4">
        <div class="col-md-6">
            <div class="card shadow bg-info text-white">
                <div class="card-body">
                    <h5>Total Courses Assigned</h5>
                    <p><?php echo $total_courses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow bg-success text-white">
                <div class="card-body">
                    <h5>Total Students Across Courses</h5>
                    <p><?php echo $total_students; ?></p>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Manage Attendance Section -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h3>Manage Attendance</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Students Enrolled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)) : ?>
                        <?php foreach ($courses as $course) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($course['student_count']); ?> Student(s)</td>
                                <td>
                                    <div class="attendance-btn">
                                        <a href="mark_attendance.php?course_id=<?php echo urlencode($course['course_code']); ?>" class="btn btn-primary">Mark Attendance</a>
                                        <a href="view_attendance.php?course_id=<?php echo urlencode($course['course_code']); ?>" class="btn btn-secondary">View Attendance Report</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No courses assigned.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
