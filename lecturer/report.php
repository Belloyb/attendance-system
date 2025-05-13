<?php
require 'Database.php';
require 'LecturerReport.php';
session_start();

// Ensure the user is logged in and has the lecturer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: ../index.php?error=Access denied. Please log in as a lecturer.');
    exit();
}

// Instantiate required classes
$db = new Database();
$report = new LecturerReport($db);

// Get the logged-in lecturer's ID
$lecturerId = $_SESSION['user_id'];

// Fetch courses assigned to the lecturer for the dropdown
$courses = $report->getAssignedCourses($lecturerId);

// Initialize report data
$reportData = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['course_id'])) {
    $courseId = intval($_GET['course_id']);
    $reportData = $report->getAttendanceReport($courseId, $lecturerId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Attendance Report</h1>
    <form method="GET" action="report.php" class="mb-4">
        <label for="course">Select Course:</label>
        <select id="course" name="course_id" class="form-control w-50" required>
            <option value="">-- Select Course --</option>
            <?php while ($course = $courses->fetch_assoc()): ?>
                <option value="<?= $course['course_id'] ?>"><?= $course['course_name'] ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
    </form>

    <?php if ($reportData): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Level</th>
                    <th>Attendance (%)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $reportData->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['student_id'] ?></td>
                        <td><?= $row['student_name'] ?></td>
                        <td><?= $row['student_level'] ?></td>
                        <td><?= $row['attendance_percentage'] ?>%</td>
                        <td><?= $row['eligibility_status'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['course_id'])): ?>
            <p>No attendance records found for the selected course.</p>
        <?php else: ?>
            <p>Select a course to generate a report.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
