<?php
session_start();

require_once '../includes/db.php';  // Ensure this file establishes the $db connection properly
include '../includes/auth.php';     // Authentication check
include '../includes/header.php';   // Header
require '../models/Course.php';     // Course model
require '../models/Attendance.php'; // Attendance model

// Check session expiry for inactivity
$session_lifetime = 30 * 60; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_lifetime) {
    session_unset();     // Unset all session variables
    session_destroy();   // Destroy the session
    header('Location: ../index.php'); // Redirect to login page
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time

// Ensure only lecturers can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: ../index.php');
    exit();
}

// Check if user ID is set in the session
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');  // Redirect to login if not logged in
    exit();
}

$lecturer_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Display header specific to lecturer
require_once('../includes/header.php');
pageHeader::display($role);

// Fetch lecturer-specific courses and attendance records
$courseModel = new Course($db);  // Use $db (PDO connection passed in from db.php)
$attendanceModel = new Attendance($db); // Attendance model, pass the PDO connection

// Get the courses assigned to this lecturer
$courses = $courseModel->getCoursesByLecturer($lecturer_id);

// Fetch total attendance records for courses taught by the lecturer
$totalAttendanceRecords = $attendanceModel->getTotalAttendanceForLecturer($lecturer_id);
?>

<div class="container mt-5">
    <h1>Lecturer Dashboard</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Courses Taught</h5>
                    <ul class="list-group">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <li class="list-group-item"><?php echo htmlspecialchars($course['course_name']); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No courses assigned</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Attendance Records</h5>
                    <p class="card-text"><?php echo $totalAttendanceRecords; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; 
Footer::display();
?>
